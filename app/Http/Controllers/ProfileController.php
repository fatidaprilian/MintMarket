<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request)
    {
        // Panggil API untuk mendapatkan daftar kota
        $citiesResponse = Http::withoutVerifying()->get('https://api.nusakita.yuefii.site/v2/kab-kota?pagination=false');

        $cities = [];
        // Cek jika request berhasil dan memiliki data
        if ($citiesResponse->successful() && isset($citiesResponse->json()['data'])) {
            $cities = $citiesResponse->json()['data'];
        }

        return view('profile.edit', [
            'user' => $request->user(),
            'cities' => $cities, // Kirim data kota ke view
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        Log::info('Tes log dari ProfileController update method.');

        $user = $request->user();

        // Ambil semua data yang sudah divalidasi, KECUALI file 'profile_picture'
        $validatedData = $request->validated();

        if (isset($validatedData['profile_picture'])) {
            unset($validatedData['profile_picture']);
        }

        // Isi model user dengan data yang sudah divalidasi (non-file)
        $user->fill($validatedData);

        // --- MULAI LOGGING DETAIL UNTUK UPLOAD GAMBAR ---
        Log::info('ProfileController@update: Request has file "profile_picture": ' . ($request->hasFile('profile_picture') ? 'true' : 'false'));

        if ($request->hasFile('profile_picture')) {
            try {
                // Hapus gambar lama jika ada (lokal)
                if ($user->profile_picture && env('FILE_STORAGE_DISK', 'public') === 'public') {
                    Log::info('ProfileController@update: Deleting old profile picture: ' . $user->profile_picture);
                    Storage::disk('public')->delete($user->profile_picture);
                }

                $disk = env('FILE_STORAGE_DISK', 'public');
                if ($disk === 'public') {
                    // Upload ke storage lokal
                    $path = $request->file('profile_picture')->store('profile_pictures', 'public');
                    $user->profile_picture = $path;
                    Log::info('ProfileController@update: New profile picture stored at: ' . $path);
                } else if ($disk === 'vercel_blob') {
                    // Upload ke Vercel Blob
                    $file = $request->file('profile_picture');
                    $filename = 'profile_pictures/' . time() . '_' . $file->getClientOriginalName();
                    $blobUrl = env('VERCEL_BLOB_URL') . '/' . $filename;

                    $response = Http::withToken(env('BLOB_READ_WRITE_TOKEN'))
                        ->put($blobUrl, fopen($file->getRealPath(), 'r'));

                    if ($response->successful()) {
                        $user->profile_picture = $filename; // Simpan filename (bukan URL penuh)
                        Log::info('ProfileController@update: New profile picture uploaded to Vercel Blob: ' . $blobUrl);
                    } else {
                        Log::error('ProfileController@update: Error during blob upload: ' . $response->body());
                        return back()->withErrors(['profile_picture' => 'Upload ke Blob gagal.']);
                    }
                }
            } catch (\Exception $e) {
                Log::error('ProfileController@update: Error during file storage: ' . $e->getMessage());
                Log::error('ProfileController@update: Stack trace: ' . $e->getTraceAsString());
            }
        }
        // --- AKHIR LOGGING DETAIL UPLOAD GAMBAR ---

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // --- LOGGING SEBELUM SAVE ---
        Log::info('ProfileController@update: User model is dirty for profile_picture: ' . ($user->isDirty('profile_picture') ? 'true' : 'false'));
        Log::info('ProfileController@update: Current user profile_picture value: ' . ($user->profile_picture ?? 'NULL'));

        $saveResult = $user->save(); // Simpan semua perubahan ke database
        Log::info('ProfileController@update: User model save result: ' . ($saveResult ? 'true' : 'false'));

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        // Hapus file profile_picture jika ada (hanya di lokal)
        if ($user->profile_picture && env('FILE_STORAGE_DISK', 'public') === 'public') {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
