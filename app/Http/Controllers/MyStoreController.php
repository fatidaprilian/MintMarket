<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon; // Import Carbon untuk manipulasi tanggal

class MyStoreController extends Controller
{
    /**
     * Display a listing of the user's store dashboard.
     */
    public function index()
    {
        return $this->dashboard();
    }

    /**
     * Display store dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $store = $user->store;

        if (!$store) {
            return redirect()->route('store.create')
                ->with('error', 'Anda belum memiliki toko. Silakan buat toko terlebih dahulu.');
        }

        // Transaction statistics
        $totalTransactions = $store->transactions()->count();
        $completedTransactions = $store->transactions()->where('status', 'completed')->count();
        $pendingTransactions = $store->transactions()->whereIn('status', ['pending', 'processing'])->count();

        // Product statistics
        $totalProducts = $store->products()->count();
        $availableProducts = $store->products()->available()->count();

        // Reviews (placeholder - you might want to implement this later)
        $totalReviews = 0; // $store->getTotalReviews(); // if you have review system

        // Get recent transactions (last 5)
        $recentTransactions = $store->transactions()
            ->with(['user'])
            ->latest()
            ->take(5)
            ->get();

        // Get top products (by stock or you can implement sales count later)
        $topProducts = $store->products()
            ->available()
            ->orderBy('stock', 'desc')
            ->take(5)
            ->get();

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

    /**
     * Show the form for creating a new store.
     */
    public function create()
    {
        // Jika user sudah punya toko, redirect ke halaman edit
        if (Auth::user()->hasStore()) {
            return redirect()->route('store.edit')->with('info', 'Anda sudah memiliki toko. Anda bisa mengedit profil toko Anda di sini.');
        }

        // Inisialisasi data default untuk form jika membuat baru
        $operatingHours = [
            ['day' => 'Senin', 'open' => '09:00', 'close' => '17:00'],
            ['day' => 'Selasa', 'open' => '09:00', 'close' => '17:00'],
            ['day' => 'Rabu', 'open' => '09:00', 'close' => '17:00'],
            ['day' => 'Kamis', 'open' => '09:00', 'close' => '17:00'],
            ['day' => 'Jumat', 'open' => '09:00', 'close' => '17:00'],
            ['day' => 'Sabtu', 'open' => '09:00', 'close' => '17:00'],
            ['day' => 'Minggu', 'open' => 'Libur', 'close' => 'Libur'],
        ];

        $flashSaleSessions = $this->generateFlashSaleScheduleOptions();

        return view('my-store.profile-form', [
            'store' => null, // Tidak ada toko yang diedit
            'operatingHours' => $operatingHours,
            'isCreating' => true, // Flag untuk UI
            // Tambahkan data flash sale
            'flashSaleSessions' => $flashSaleSessions,
            'selectedFlashSaleDate' => null, // Tidak ada yang terpilih saat membuat baru
        ]);
    }

    /**
     * Store a newly created store in database.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Check if user already has a store
        if ($user->hasStore()) {
            return redirect()->route('store.index')->with('error', 'Anda sudah memiliki toko.');
        }

        // Validasi data
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('stores', 'name')],
            'description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|max:1024', // Max 1MB
            'banner' => 'nullable|image|max:2048', // Max 2MB
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'postal_code' => 'required|string|max:10',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'email_store' => 'nullable|email|max:255',
            'store_type' => 'nullable|string|max:255',
            'operating_hours' => 'nullable|array',
            'operating_hours.*.day' => 'required_with:operating_hours|string',
            'operating_hours.*.open' => 'required_with:operating_hours|string',
            'operating_hours.*.close' => 'required_with:operating_hours|string',
            'instagram' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
            'terms_and_conditions' => 'nullable|string|max:5000',
        ]);

        $data = $request->except(['logo', 'banner', 'email_store']); // Kecualikan file dan email_store sementara
        $data['slug'] = Str::slug($request->name);
        $data['email'] = $request->email_store; // Pastikan email toko tersimpan ke kolom 'email'
        $data['operating_hours'] = json_encode($request->operating_hours); // Simpan sebagai JSON

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('stores/logos', 'public');
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            $data['banner'] = $request->file('banner')->store('stores/banners', 'public');
        }

        $user->store()->create($data);

        return redirect()->route('store.index')->with('success', 'Toko berhasil dibuka! Selamat berjualan.');
    }

    /**
     * Show the form for editing the specified store.
     */
    public function edit()
    {
        $store = Auth::user()->store;

        // Jika user belum punya toko, redirect ke halaman buat toko
        if (!$store) {
            return redirect()->route('store.create')->with('error', 'Anda belum memiliki toko. Silakan buat toko terlebih dahulu.');
        }

        // Pastikan operating_hours di-decode jika tersimpan sebagai JSON string
        $operatingHours = is_string($store->operating_hours) ? json_decode($store->operating_hours, true) : $store->operating_hours;
        $operatingHours = $operatingHours ?? [
            ['day' => 'Senin', 'open' => '09:00', 'close' => '17:00'],
            ['day' => 'Selasa', 'open' => '09:00', 'close' => '17:00'],
            ['day' => 'Rabu', 'open' => '09:00', 'close' => '17:00'],
            ['day' => 'Kamis', 'open' => '09:00', 'close' => '17:00'],
            ['day' => 'Jumat', 'open' => '09:00', 'close' => '17:00'],
            ['day' => 'Sabtu', 'open' => '09:00', 'close' => '17:00'],
            ['day' => 'Minggu', 'open' => 'Libur', 'close' => 'Libur'],
        ];

        $flashSaleSessions = $this->generateFlashSaleScheduleOptions();

        return view('my-store.profile-form', [
            'store' => $store,
            'operatingHours' => $operatingHours,
            'isCreating' => false,
            // Tambahkan data flash sale
            'flashSaleSessions' => $flashSaleSessions,
            'selectedFlashSaleDate' => $store->flash_sale_end_date ? $store->flash_sale_end_date->format('Y-m-d H:i:s') : null,
        ]);
    }

    /**
     * Update the specified store in storage.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $store = $user->store;

        if (!$store) {
            return redirect()->route('store.create')->with('error', 'Anda belum memiliki toko. Silakan buat toko terlebih dahulu.');
        }

        // Validasi data
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('stores', 'name')->ignore($store->id)],
            'description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|max:1024', // Max 1MB
            'banner' => 'nullable|image|max:2048', // Max 2MB
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'postal_code' => 'required|string|max:10',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'email_store' => 'nullable|email|max:255',
            'store_type' => 'nullable|string|max:255',
            'operating_hours' => 'nullable|array',
            'operating_hours.*.day' => 'required_with:operating_hours|string',
            'operating_hours.*.open' => 'required_with:operating_hours|string',
            'operating_hours.*.close' => 'required_with:operating_hours|string',
            'instagram' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
            'terms_and_conditions' => 'nullable|string|max:5000',
        ]);

        $data = $request->except(['logo', 'banner', 'email_store']);
        $data['slug'] = Str::slug($request->name);
        $data['email'] = $request->email_store;
        $data['operating_hours'] = json_encode($request->operating_hours);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($store->logo && Storage::disk('public')->exists($store->logo)) {
                Storage::disk('public')->delete($store->logo);
            }
            $data['logo'] = $request->file('logo')->store('stores/logos', 'public');
        } elseif ($request->input('remove_logo')) { // Logika untuk menghapus logo yang sudah ada
            if ($store->logo && Storage::disk('public')->exists($store->logo)) {
                Storage::disk('public')->delete($store->logo);
            }
            $data['logo'] = null;
        } else {
            $data['logo'] = $store->logo; // Pertahankan logo lama jika tidak ada upload baru atau permintaan hapus
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            // Hapus banner lama jika ada
            if ($store->banner && Storage::disk('public')->exists($store->banner)) {
                Storage::disk('public')->delete($store->banner);
            }
            $data['banner'] = $request->file('banner')->store('stores/banners', 'public');
        } elseif ($request->input('remove_banner')) { // Logika untuk menghapus banner yang sudah ada
            if ($store->banner && Storage::disk('public')->exists($store->banner)) {
                Storage::disk('public')->delete($store->banner);
            }
            $data['banner'] = null;
        } else {
            $data['banner'] = $store->banner; // Pertahankan banner lama
        }

        $store->update($data);

        return redirect()->route('store.index')->with('success', 'Profil toko berhasil diperbarui!');
    }

    // =========================================================================
    // Metode-metode Manajemen Produk Toko
    // =========================================================================

    public function products()
    {
        $store = Auth::user()->store;
        if (!$store) {
            return redirect()->route('store.create')->with('error', 'Anda belum memiliki toko.');
        }
        $products = $store->products()->latest()->paginate(10);
        return view('my-store.products.index', compact('store', 'products'));
    }

    public function createProduct()
    {
        $store = Auth::user()->store;
        if (!$store) {
            return redirect()->route('store.create')->with('error', 'Anda belum memiliki toko.');
        }
        $categories = Category::all(); // Menggunakan model Category

        // Logika untuk menentukan sesi Flash Sale berikutnya
        $flashSaleSessions = $this->generateFlashSaleScheduleOptions();

        return view('my-store.products.create', compact('store', 'categories', 'flashSaleSessions'));
    }

    public function storeProduct(Request $request)
    {
        $store = Auth::user()->store;
        if (!$store) {
            return redirect()->route('store.create')->with('error', 'Anda belum memiliki toko.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:5000',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'condition' => 'required|string',
            'image.*' => 'nullable|image|max:2048', // Max 2MB per gambar
            'status' => 'required|in:tersedia,terjual,menunggu_verifikasi,habis',
            'is_active' => 'boolean',
            'flash_sale_price' => 'nullable|numeric|min:0',
            // Validasi flash_sale_end_date sekarang akan memeriksa format Y-m-d H:i:s
            'flash_sale_end_date' => 'nullable|date_format:Y-m-d H:i:s|after:now',
        ]);

        $data = $request->except(['image']);
        $data['slug'] = Str::slug($request->name);
        $data['is_active'] = $request->has('is_active');
        // Set status produk baru menjadi 'menunggu_verifikasi'
        $data['status'] = 'menunggu_verifikasi';


        if ($request->hasFile('image')) {
            $images = [];
            foreach ($request->file('image') as $file) {
                // Simpan gambar ke folder 'product-images'
                $images[] = $file->store('product-images', 'public');
            }
            // Filter out any nulls or empty strings that might accidentally creep in
            $data['image'] = array_filter($images);
        } else {
            $data['image'] = []; // Ensure it's an empty array if no images are uploaded
        }

        $store->products()->create($data);

        return redirect()->route('store.products.index')->with('success', 'Produk berhasil ditambahkan! Menunggu verifikasi.');
    }

    public function editProduct(Product $product)
    {
        $store = Auth::user()->store;
        if (!$store || $product->store_id !== $store->id) {
            abort(403, 'Unauthorized action.');
        }
        $categories = Category::all();

        // Logika untuk menentukan sesi Flash Sale berikutnya
        // Sertakan tanggal berakhir flash sale produk saat ini dalam opsi
        $flashSaleSessions = $this->generateFlashSaleScheduleOptions($product->flash_sale_end_date);
        $selectedFlashSaleDate = $product->flash_sale_end_date ? $product->flash_sale_end_date->format('Y-m-d H:i:s') : null;

        return view('my-store.products.edit', compact('store', 'product', 'categories', 'flashSaleSessions', 'selectedFlashSaleDate'));
    }

    public function updateProduct(Request $request, Product $product)
    {
        $store = Auth::user()->store;
        if (!$store || $product->store_id !== $store->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
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
            // Validasi flash_sale_end_date sekarang akan memeriksa format Y-m-d H:i:s
            'flash_sale_end_date' => 'nullable|date_format:Y-m-d H:i:s|after:now',
            'remove_images' => 'nullable|array', // Untuk menghapus gambar yang sudah ada
        ]);

        $data = $request->except(['image', 'remove_images']);
        $data['slug'] = Str::slug($request->name);
        $data['is_active'] = $request->has('is_active');

        $currentImages = $product->image ?? [];
        // Ensure currentImages are always filtered to avoid null paths
        $currentImages = array_filter($currentImages);
        $imagesToKeep = [];

        // Hapus gambar yang diminta
        if ($request->has('remove_images')) {
            $imagesToRemove = array_filter($request->input('remove_images')); // Filter nulls from removal list
            foreach ($imagesToRemove as $imagePath) {
                // Pastikan path yang dihapus sesuai dengan direktori 'product-images'
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
            }
            $imagesToKeep = array_diff($currentImages, $imagesToRemove);
        } else {
            $imagesToKeep = $currentImages;
        }

        // Tambahkan gambar baru
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $file) {
                // Simpan gambar baru ke folder 'product-images'
                $imagesToKeep[] = $file->store('product-images', 'public');
            }
        }
        // Final filter to ensure no nulls remain in the image array stored
        $data['image'] = array_filter($imagesToKeep);

        $product->update($data);

        return redirect()->route('store.products.index')->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroyProduct(Product $product)
    {
        $store = Auth::user()->store;
        if (!$store || $product->store_id !== $store->id) {
            abort(403, 'Unauthorized action.');
        }

        // Hapus gambar produk dari storage
        if ($product->image) {
            // Filter null values from the image array before iterating
            $imagesToDelete = array_filter($product->image);
            foreach ($imagesToDelete as $imagePath) {
                // Pastikan path yang dihapus sesuai dengan direktori 'product-images'
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
            }
        }

        $product->delete();
        return redirect()->route('store.products.index')->with('success', 'Produk berhasil dihapus!');
    }

    // =========================================================================
    // Metode-metode Analitik Toko
    // =========================================================================

    public function analytics()
    {
        $store = Auth::user()->store;
        if (!$store) {
            return redirect()->route('store.create')->with('error', 'Anda belum memiliki toko.');
        }

        // Logika untuk menampilkan analitik toko (contoh sederhana)
        $totalSalesCount = $store->transactions()->where('status', 'completed')->count();
        $totalRevenue = $store->transactions()->where('status', 'completed')->sum('total_amount');

        // Mendapatkan produk paling laris dari toko ini
        $mostSoldProduct = Product::whereHas('store', function ($q) use ($store) {
            $q->where('id', $store->id);
        })->withCount('transactions')->orderBy('transactions_count', 'desc')->first();

        return view('my-store.analytics', compact('store', 'totalSalesCount', 'totalRevenue', 'mostSoldProduct'));
    }

    public function promotions()
    {
        $store = Auth::user()->store;
        if (!$store) {
            return redirect()->route('store.create')->with('error', 'Anda belum memiliki toko.');
        }

        return view('my-store.promotions', compact('store'));
    }

    /**
     * Helper function to generate flash sale schedule options
     * @param \Carbon\Carbon|null $currentFlashSaleEndDate The current product's flash sale end date for pre-selection
     * @return array
     */
    private function generateFlashSaleScheduleOptions(?Carbon $currentFlashSaleEndDate = null): array
    {
        $scheduleHours = [9, 22]; // Start hours for flash sales (e.g., 9 AM, 10 PM WIB)
        $durationHours = 14; // Duration of each flash sale session
        $options = [];
        $now = now();

        // Add a default "Tidak ada flash sale" option
        $options[''] = 'Tidak ada flash sale';

        // Include current product's flash sale end date if applicable and not already in future slots
        if ($currentFlashSaleEndDate && $currentFlashSaleEndDate->isFuture()) {
            // Calculate session start time for the existing flash sale
            $existingSessionStart = $currentFlashSaleEndDate->copy()->subHours($durationHours);
            $options[$currentFlashSaleEndDate->format('Y-m-d H:i:s')] = 'Sesi Aktif/Terpilih: ' . $existingSessionStart->format('d M, H:i') . ' WIB (Berakhir: ' . $currentFlashSaleEndDate->format('H:i') . ' WIB)';
        } elseif ($currentFlashSaleEndDate && $currentFlashSaleEndDate->isPast()) {
            // If the existing flash sale date is in the past, offer it as an option if it's the product's current setting.
            // This ensures the current value remains selected on form load if it's expired.
            $expiredSessionStart = $currentFlashSaleEndDate->copy()->subHours($durationHours);
            $options[$currentFlashSaleEndDate->format('Y-m-d H:i:s')] = 'Sesi Kadaluarsa: ' . $expiredSessionStart->format('d M, H:i') . ' WIB (Berakhir: ' . $currentFlashSaleEndDate->format('H:i') . ' WIB)';
        }


        // Generate upcoming sessions for today and tomorrow
        $datesToConsider = [$now, $now->copy()->addDay()];

        foreach ($datesToConsider as $date) {
            foreach ($scheduleHours as $hour) {
                $sessionStart = $date->copy()->setTime($hour, 0, 0);
                $sessionEnd = $sessionStart->copy()->addHours($durationHours);

                // Only add sessions that start in the future
                if ($sessionStart->isFuture()) {
                    // Avoid duplicating the current active/selected session if it's also a future scheduled one
                    if (!isset($options[$sessionEnd->format('Y-m-d H:i:s')])) {
                        $options[$sessionEnd->format('Y-m-d H:i:s')] = 'Sesi Mulai: ' . $sessionStart->format('d M, H:i') . ' WIB (Berakhir: ' . $sessionEnd->format('H:i') . ' WIB)';
                    }
                }
            }
        }

        // Sort options by date key (the 'Y-m-d H:i:s' string)
        ksort($options);

        return $options;
    }
}
