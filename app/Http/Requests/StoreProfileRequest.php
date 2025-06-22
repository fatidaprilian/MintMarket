<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Basic Info (Required)
            'name' => 'required|string|max:255|unique:stores,name,' . optional($this->store)->id,
            'description' => 'required|string|min:50|max:1000',
            'store_type' => 'required|in:individual,business',

            // Address (Required)
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string|min:10|max:500',

            // Contact (Required)
            'phone' => 'required|string|min:10|max:15|regex:/^[0-9+\-\(\)\s]+$/',

            // Images
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',

            // Optional Contact
            'whatsapp' => 'nullable|string|min:10|max:15|regex:/^[0-9+\-\(\)\s]+$/',
            'email' => 'nullable|email|max:255',

            // Social Media
            'instagram' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',

            // Operating Hours
            'operating_hours' => 'nullable|array',
            'operating_hours.*.day' => 'required|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'operating_hours.*.open' => 'nullable|date_format:H:i',
            'operating_hours.*.close' => 'nullable|date_format:H:i|after:operating_hours.*.open',
            'operating_hours.*.is_closed' => 'boolean',

            // Terms
            'terms_and_conditions' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama toko wajib diisi',
            'name.unique' => 'Nama toko sudah digunakan',
            'description.required' => 'Deskripsi toko wajib diisi',
            'description.min' => 'Deskripsi minimal 50 karakter',
            'province.required' => 'Provinsi wajib dipilih',
            'city.required' => 'Kota wajib dipilih',
            'address.required' => 'Alamat lengkap wajib diisi',
            'address.min' => 'Alamat minimal 10 karakter',
            'phone.required' => 'Nomor telepon wajib diisi',
            'phone.regex' => 'Format nomor telepon tidak valid',
            'logo.image' => 'Logo harus berupa gambar',
            'logo.max' => 'Logo maksimal 2MB',
            'banner.image' => 'Banner harus berupa gambar',
            'banner.max' => 'Banner maksimal 5MB',
        ];
    }
}
