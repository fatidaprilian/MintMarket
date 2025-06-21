<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Pastikan ini di-import

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
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
                // Hapus gambar lama jika ada
                if ($user->profile_picture) {
                    Log::info('ProfileController@update: Deleting old profile picture: ' . $user->profile_picture);
                    Storage::disk('public')->delete($user->profile_picture);
                }

                // Simpan gambar baru
                $path = $request->file('profile_picture')->store('profile_pictures', 'public');
                $user->profile_picture = $path; // Tetapkan path gambar baru ke model
                Log::info('ProfileController@update: New profile picture stored at: ' . $path);
            } catch (\Exception $e) {
                Log::error('ProfileController@update: Error during file storage: ' . $e->getMessage());
                // Mungkin juga ada detail lain seperti code, file, line:
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

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
