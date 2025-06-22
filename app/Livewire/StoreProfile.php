<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StoreProfile extends Component
{
    use WithFileUploads;

    // --- TAMBAHKAN BARIS INI ---
    protected static string $layout = 'layouts.app';
    // --------------------------

    public $storeId;
    public $name;
    public $slug;
    public $description;
    public $logo;
    public $banner;
    public $province;
    public $city;
    public $address;
    public $postal_code;
    public $phone;
    public $whatsapp;
    public $email_store;
    public $store_type;
    public $operating_hours = [];
    public $instagram;
    public $facebook;
    public $tiktok;
    public $terms_and_conditions;
    public $rating;
    public $is_verified;

    public $newLogo;
    public $newBanner;

    public $message = '';

    // protected $listeners = ['filePondUpload' => 'handleFilePondUpload']; // Jika tidak pakai FilePond, ini bisa dihapus

    public function mount()
    {
        $user = Auth::user();
        // Menggunakan relasi hasOne dari model User ke Store
        $store = $user->store;

        if ($store) {
            $this->storeId = $store->id;
            $this->name = $store->name;
            $this->slug = $store->slug;
            $this->description = $store->description;
            $this->logo = $store->logo;
            $this->banner = $store->banner;
            $this->province = $store->province;
            $this->city = $store->city;
            $this->address = $store->address;
            $this->postal_code = $store->postal_code;
            $this->phone = $store->phone;
            $this->whatsapp = $store->whatsapp;
            $this->email_store = $store->email;
            $this->store_type = $store->store_type;
            $this->operating_hours = $store->operating_hours ?? [];
            $this->instagram = $store->instagram;
            $this->facebook = $store->facebook;
            $this->tiktok = $store->tiktok;
            $this->terms_and_conditions = $store->terms_and_conditions;
            $this->rating = $store->rating;
            $this->is_verified = $store->is_verified;
        } else {
            $this->is_verified = false;
            $this->rating = 0.00;
            $this->operating_hours = [
                ['day' => 'Senin', 'open' => '09:00', 'close' => '17:00'],
                ['day' => 'Selasa', 'open' => '09:00', 'close' => '17:00'],
                ['day' => 'Rabu', 'open' => '09:00', 'close' => '17:00'],
                ['day' => 'Kamis', 'open' => '09:00', 'close' => '17:00'],
                ['day' => 'Jumat', 'open' => '09:00', 'close' => '17:00'],
                ['day' => 'Sabtu', 'open' => '09:00', 'close' => '17:00'],
                ['day' => 'Minggu', 'open' => 'Libur', 'close' => 'Libur'],
            ];
        }
    }

    protected function rules()
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('stores')->ignore($this->storeId),
            ],
            'description' => 'nullable|string|max:1000',
            'newLogo' => 'nullable|image|max:1024',
            'newBanner' => 'nullable|image|max:2048',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'postal_code' => 'required|string|max:10',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'email_store' => 'nullable|email|max:255',
            'store_type' => 'nullable|string|max:255',
            'operating_hours.*.day' => 'required|string',
            'operating_hours.*.open' => 'required|string',
            'operating_hours.*.close' => 'required|string',
            'instagram' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
            'terms_and_conditions' => 'nullable|string|max:5000',
        ];

        return $rules;
    }

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
    }

    public function saveStore()
    {
        $this->validate();

        $user = Auth::user();
        $store = $user->store;

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'province' => $this->province,
            'city' => $this->city,
            'address' => $this->address,
            'postal_code' => $this->postal_code,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'email' => $this->email_store,
            'store_type' => $this->store_type,
            'operating_hours' => $this->operating_hours,
            'instagram' => $this->instagram,
            'facebook' => $this->facebook,
            'tiktok' => $this->tiktok,
            'terms_and_conditions' => $this->terms_and_conditions,
        ];

        if ($this->newLogo) {
            if ($store && $store->logo && Storage::disk('public')->exists($store->logo)) {
                Storage::disk('public')->delete($store->logo);
            }
            $data['logo'] = $this->newLogo->store('stores/logos', 'public');
        } elseif ($this->logo && !$this->newLogo) {
            $data['logo'] = $this->logo;
        } else {
            $data['logo'] = null;
        }

        if ($this->newBanner) {
            if ($store && $store->banner && Storage::disk('public')->exists($store->banner)) {
                Storage::disk('public')->delete($store->banner);
            }
            $data['banner'] = $this->newBanner->store('stores/banners', 'public');
        } elseif ($this->banner && !$this->newBanner) {
            $data['banner'] = $this->banner;
        } else {
            $data['banner'] = null;
        }


        if ($store) {
            $store->update($data);
            $this->message = 'Profil toko berhasil diperbarui!';
        } else {
            $store = $user->store()->create($data);
            $this->storeId = $store->id;
            $this->message = 'Toko berhasil dibuka! Selamat berjualan.';
        }

        $this->newLogo = null;
        $this->newBanner = null;

        $this->dispatch('store-updated');
    }

    public function removeLogo()
    {
        if ($this->logo) {
            if (Storage::disk('public')->exists($this->logo)) {
                Storage::disk('public')->delete($this->logo);
            }
            $this->logo = null;
            $this->newLogo = null;
            $this->message = 'Logo toko berhasil dihapus.';
        }
    }

    public function removeBanner()
    {
        if ($this->banner) {
            if (Storage::disk('public')->exists($this->banner)) {
                Storage::disk('public')->delete($this->banner);
            }
            $this->banner = null;
            $this->newBanner = null;
            $this->message = 'Banner toko berhasil dihapus.';
        }
    }

    public function addOperatingHour()
    {
        $this->operating_hours[] = ['day' => '', 'open' => '', 'close' => ''];
    }

    public function removeOperatingHour($index)
    {
        unset($this->operating_hours[$index]);
        $this->operating_hours = array_values($this->operating_hours);
    }

    public function render()
    {
        return view('livewire.store-profile');
    }
}
