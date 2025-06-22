<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class MyStoreController extends Controller
{
    // =========================================================================
    // DASHBOARD
    // =========================================================================

    public function index()
    {
        return $this->dashboard();
    }

    public function dashboard()
    {
        $user = Auth::user();
        $store = $user->store;

        if (!$store) {
            return redirect()->route('store.create')
                ->with('error', 'Anda belum memiliki toko. Silakan buat toko terlebih dahulu.');
        }

        $totalTransactions = $store->transactions()->count();
        $completedTransactions = $store->transactions()->whereIn('status', ['completed', 'delivered'])->count();
        $pendingTransactions = $store->transactions()->whereIn('status', ['pending', 'paid', 'processing'])->count();
        $totalProducts = $store->products()->count();
        $availableProducts = $store->products()->available()->count();
        $totalReviews = 0;
        $recentTransactions = $store->transactions()->with(['user'])->latest()->take(5)->get();
        $topProducts = $store->products()->available()->orderBy('stock', 'desc')->take(5)->get();

        return view('my-store.dashboard', compact(
            'store',
            'totalTransactions',
            'completedTransactions',
            'pendingTransactions',
            'totalProducts',
            'availableProducts',
            'totalReviews',
            'recentTransactions',
            'topProducts'
        ));
    }

    // =========================================================================
    // STORE PROFILE MANAGEMENT
    // =========================================================================

    public function create()
    {
        if (Auth::user()->hasStore()) {
            return redirect()->route('store.edit')->with('info', 'Anda sudah memiliki toko.');
        }
        $operatingHours = [['day' => 'Senin', 'open' => '09:00', 'close' => '17:00'], ['day' => 'Selasa', 'open' => '09:00', 'close' => '17:00'], ['day' => 'Rabu', 'open' => '09:00', 'close' => '17:00'], ['day' => 'Kamis', 'open' => '09:00', 'close' => '17:00'], ['day' => 'Jumat', 'open' => '09:00', 'close' => '17:00'], ['day' => 'Sabtu', 'open' => '09:00', 'close' => '17:00'], ['day' => 'Minggu', 'open' => 'Libur', 'close' => 'Libur']];
        $flashSaleSessions = $this->generateFlashSaleScheduleOptions();
        return view('my-store.profile-form', ['store' => null, 'operatingHours' => $operatingHours, 'isCreating' => true, 'flashSaleSessions' => $flashSaleSessions, 'selectedFlashSaleDate' => null]);
    }

    public function store(Request $request)
    {
        if (Auth::user()->hasStore()) {
            return redirect()->route('store.index')->with('error', 'Anda sudah memiliki toko.');
        }
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('stores', 'name')],
            'description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|max:1024',
            'banner' => 'nullable|image|max:2048',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'postal_code' => 'required|string|max:10',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'email_store' => 'nullable|email|max:255',
            'store_type' => 'nullable|string|max:255',
            'operating_hours' => 'nullable|array',
            'instagram' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
            'terms_and_conditions' => 'nullable|string|max:5000',
        ]);
        $data = $validatedData;
        $data['slug'] = Str::slug($request->name);
        $data['email'] = $request->email_store;
        $data['operating_hours'] = json_encode($request->operating_hours);
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('stores/logos', 'public');
        }
        if ($request->hasFile('banner')) {
            $data['banner'] = $request->file('banner')->store('stores/banners', 'public');
        }
        Auth::user()->store()->create($data);
        return redirect()->route('store.index')->with('success', 'Toko berhasil dibuka! Selamat berjualan.');
    }

    public function edit()
    {
        $store = Auth::user()->store;
        if (!$store) {
            return redirect()->route('store.create')->with('error', 'Anda belum memiliki toko.');
        }
        $operatingHours = is_string($store->operating_hours) ? json_decode($store->operating_hours, true) : ($store->operating_hours ?? [['day' => 'Senin', 'open' => '09:00', 'close' => '17:00'], ['day' => 'Selasa', 'open' => '09:00', 'close' => '17:00'], ['day' => 'Rabu', 'open' => '09:00', 'close' => '17:00'], ['day' => 'Kamis', 'open' => '09:00', 'close' => '17:00'], ['day' => 'Jumat', 'open' => '09:00', 'close' => '17:00'], ['day' => 'Sabtu', 'open' => '09:00', 'close' => '17:00'], ['day' => 'Minggu', 'open' => 'Libur', 'close' => 'Libur']]);
        $flashSaleSessions = $this->generateFlashSaleScheduleOptions();
        return view('my-store.profile-form', ['store' => $store, 'operatingHours' => $operatingHours, 'isCreating' => false, 'flashSaleSessions' => $flashSaleSessions, 'selectedFlashSaleDate' => $store->flash_sale_end_date ? $store->flash_sale_end_date->format('Y-m-d H:i:s') : null]);
    }

    public function update(Request $request)
    {
        $store = Auth::user()->store;
        if (!$store) {
            return redirect()->route('store.create')->with('error', 'Anda belum memiliki toko.');
        }
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('stores', 'name')->ignore($store->id)],
            // Add other validations as needed
        ]);
        $data = $request->except(['_token', '_method', 'logo', 'banner', 'remove_logo', 'remove_banner']);
        $data['slug'] = Str::slug($request->name);
        $data['operating_hours'] = json_encode($request->operating_hours);
        if ($request->hasFile('logo')) {
            if ($store->logo) Storage::disk('public')->delete($store->logo);
            $data['logo'] = $request->file('logo')->store('stores/logos', 'public');
        } elseif ($request->input('remove_logo')) {
            if ($store->logo) Storage::disk('public')->delete($store->logo);
            $data['logo'] = null;
        }
        if ($request->hasFile('banner')) {
            if ($store->banner) Storage::disk('public')->delete($store->banner);
            $data['banner'] = $request->file('banner')->store('stores/banners', 'public');
        } elseif ($request->input('remove_banner')) {
            if ($store->banner) Storage::disk('public')->delete($store->banner);
            $data['banner'] = null;
        }
        $store->update($data);
        return redirect()->route('store.index')->with('success', 'Profil toko berhasil diperbarui!');
    }

    // =========================================================================
    // PRODUCT MANAGEMENT
    // =========================================================================

    public function products()
    {
        $store = Auth::user()->store;
        if (!$store) {
            return redirect()->route('store.create')->with('error', 'Anda belum memiliki toko.');
        }

        $products = $store->products()->with('category')->latest()->paginate(10);

        return view('my-store.products.index', compact('store', 'products'));
    }

    public function createProduct()
    {
        $store = Auth::user()->store;
        if (!$store) {
            return redirect()->route('store.create')->with('error', 'Anda belum memiliki toko.');
        }
        $categories = Category::all();
        $flashSaleSessions = $this->generateFlashSaleScheduleOptions();
        return view('my-store.products.create', compact('store', 'categories', 'flashSaleSessions'));
    }

    public function storeProduct(Request $request)
    {
        $store = Auth::user()->store;
        if (!$store) {
            return redirect()->route('store.create')->with('error', 'Anda belum memiliki toko.');
        }
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:5000',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'condition' => 'required|string',
            'image.*' => 'nullable|image|max:2048',
            'status' => 'required|in:tersedia,terjual,menunggu_verifikasi,habis',
            'is_active' => 'boolean',
            'flash_sale_price' => 'nullable|numeric|min:0',
            'flash_sale_end_date' => 'nullable|date_format:Y-m-d H:i:s|after:now',
        ]);
        $data['slug'] = Str::slug($request->name);
        $data['is_active'] = $request->has('is_active');
        $data['status'] = 'menunggu_verifikasi';
        if ($request->hasFile('image')) {
            $images = [];
            foreach ($request->file('image') as $file) {
                $images[] = $file->store('product-images', 'public');
            }
            $data['image'] = array_filter($images);
        } else {
            $data['image'] = [];
        }
        $store->products()->create($data);
        return redirect()->route('store.products.index')->with('success', 'Produk berhasil ditambahkan! Menunggu verifikasi.');
    }

    public function editProduct(Product $product)
    {
        $store = Auth::user()->store;
        if (!$store || $product->store_id !== $store->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }
        $categories = Category::all();
        $flashSaleSessions = $this->generateFlashSaleScheduleOptions($product->flash_sale_end_date);
        $selectedFlashSaleDate = $product->flash_sale_end_date ? $product->flash_sale_end_date->format('Y-m-d H:i:s') : null;
        return view('my-store.products.edit', compact('store', 'product', 'categories', 'flashSaleSessions', 'selectedFlashSaleDate'));
    }

    public function updateProduct(Request $request, Product $product)
    {
        $store = Auth::user()->store;
        if (!$store || $product->store_id !== $store->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:5000',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'condition' => 'required|string',
            'image.*' => 'nullable|image|max:2048',
            'status' => 'required|in:tersedia,terjual,menunggu_verifikasi,habis',
            'is_active' => 'boolean',
            'flash_sale_price' => 'nullable|numeric|min:0',
            'flash_sale_end_date' => 'nullable|date_format:Y-m-d H:i:s|after:now',
            'remove_images' => 'nullable|array',
        ]);
        $data['slug'] = Str::slug($request->name);
        $data['is_active'] = $request->has('is_active');
        $imagesToKeep = $product->image ?? [];
        if ($request->has('remove_images')) {
            $imagesToRemove = array_filter($request->input('remove_images'));
            foreach ($imagesToRemove as $imagePath) {
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
            }
            $imagesToKeep = array_diff($imagesToKeep, $imagesToRemove);
        }
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $file) {
                $imagesToKeep[] = $file->store('product-images', 'public');
            }
        }
        $data['image'] = array_values(array_filter($imagesToKeep));
        $product->update($data);
        return redirect()->route('store.products.index')->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroyProduct(Product $product)
    {
        $store = Auth::user()->store;
        if (!$store || $product->store_id !== $store->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }
        if ($product->image) {
            $imagesToDelete = array_filter($product->image);
            foreach ($imagesToDelete as $imagePath) {
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
            }
        }
        $product->delete();
        return redirect()->route('store.products.index')->with('success', 'Produk berhasil dihapus!');
    }

    // =========================================================================
    // TRANSACTION MANAGEMENT
    // =========================================================================

    public function transactions(Request $request)
    {
        $store = Auth::user()->store;
        if (!$store) {
            return redirect()->route('store.create')->with('error', 'Anda belum memiliki toko.');
        }
        $status = $request->query('status');
        $query = $store->transactions()->with('user')->latest();
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }
        $transactions = $query->paginate(10);
        $statusCounts = $store->transactions()->selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status');
        $statusCounts['all'] = $store->transactions()->count();
        return view('my-store.transactions.index', compact('store', 'transactions', 'status', 'statusCounts'));
    }

    public function showTransaction(Transaction $transaction)
    {
        $store = Auth::user()->store;
        if ($transaction->store_id !== $store->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }
        $transaction->load('user', 'items.product');
        return view('my-store.transactions.show', compact('store', 'transaction'));
    }

    public function updateTransactionStatus(Request $request, Transaction $transaction)
    {
        $store = Auth::user()->store;
        if ($transaction->store_id !== $store->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }
        $request->validate([
            'status' => ['required', Rule::in(['processing', 'shipped'])],
            'tracking_number' => ['nullable', 'string', 'max:255', 'required_if:status,shipped'],
        ]);
        $newStatus = $request->input('status');
        $currentStatus = $transaction->status;
        $message = '';
        if ($newStatus === 'processing' && in_array($currentStatus, ['pending', 'paid'])) {
            $transaction->status = 'processing';
            $message = 'Pesanan sekarang sedang diproses.';
        } elseif ($newStatus === 'shipped' && $currentStatus === 'processing') {
            $transaction->status = 'shipped';
            $transaction->tracking_number = $request->input('tracking_number');
            $message = 'Pesanan telah dikirim dengan nomor resi.';
        } else {
            return redirect()->back()->with('error', 'Perubahan status dari "' . $currentStatus . '" ke "' . $newStatus . '" tidak diizinkan.');
        }
        $transaction->save();
        return redirect()->route('store.transactions.show', $transaction)->with('success', $message);
    }

    // =========================================================================
    // STORE SETTINGS
    // =========================================================================

    public function toggleAutoProcess(Request $request)
    {
        $store = Auth::user()->store;
        if (!$store) {
            return back()->with('error', 'Toko tidak ditemukan.');
        }
        $store->auto_process_orders = !$store->auto_process_orders;
        $store->save();
        $message = $store->auto_process_orders ? 'Proses pesanan otomatis diaktifkan.' : 'Proses pesanan otomatis dinonaktifkan.';
        return back()->with('success', $message);
    }

    // =========================================================================
    // ANALYTICS & PROMOTIONS
    // =========================================================================

    public function analytics()
    {
        $store = Auth::user()->store;
        if (!$store) {
            return redirect()->route('store.create')->with('error', 'Anda belum memiliki toko.');
        }

        $completedTransactions = $store->transactions()
            ->whereIn('status', ['completed', 'delivered']);

        $totalRevenue = $completedTransactions->clone()->sum('total_amount');
        $totalOrders = $completedTransactions->clone()->count();
        $averageOrderValue = ($totalOrders > 0) ? $totalRevenue / $totalOrders : 0;

        $salesData = $store->transactions()
            ->whereIn('status', ['completed', 'delivered'])
            ->where('created_at', '>=', now()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $chartLabels = $salesData->pluck('date')->map(fn($date) => Carbon::parse($date)->format('d M'));
        $chartData = $salesData->pluck('total');

        // Produk Terlaris
        $topProducts = Product::where('products.store_id', $store->id)
            ->join('transaction_items', 'products.id', '=', 'transaction_items.product_id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->whereIn('transactions.status', ['completed', 'delivered'])
            ->select('products.*', DB::raw('SUM(transaction_items.quantity) as items_sold'))
            ->groupBy('products.id')
            ->orderByDesc('items_sold')
            ->take(5)
            ->get();

        return view('my-store.analytics', compact(
            'store',
            'totalRevenue',
            'totalOrders',
            'averageOrderValue',
            'chartLabels',
            'chartData',
            'topProducts'
        ));
    }

    public function promotions()
    {
        // ... Logic for promotions page
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    private function generateFlashSaleScheduleOptions(?Carbon $currentFlashSaleEndDate = null): array
    {
        $scheduleHours = [9, 22];
        $durationHours = 14;
        $options = ['' => 'Tidak ada flash sale'];
        $now = now();
        if ($currentFlashSaleEndDate && $currentFlashSaleEndDate->isFuture()) {
            $existingSessionStart = $currentFlashSaleEndDate->copy()->subHours($durationHours);
            $options[$currentFlashSaleEndDate->format('Y-m-d H:i:s')] = 'Sesi Aktif: Berakhir ' . $currentFlashSaleEndDate->format('d M, H:i') . ' WIB';
        }
        $datesToConsider = [$now, $now->copy()->addDay()];
        foreach ($datesToConsider as $date) {
            foreach ($scheduleHours as $hour) {
                $sessionStart = $date->copy()->setTime($hour, 0, 0);
                $sessionEnd = $sessionStart->copy()->addHours($durationHours);
                if ($sessionStart->isFuture()) {
                    if (!isset($options[$sessionEnd->format('Y-m-d H:i:s')])) {
                        $options[$sessionEnd->format('Y-m-d H:i:s')] = 'Sesi Mulai: ' . $sessionStart->format('d M, H:i') . ' WIB';
                    }
                }
            }
        }
        ksort($options);
        return $options;
    }
}
