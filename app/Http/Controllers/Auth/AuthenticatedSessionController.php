<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // --- MULAI LOGIKA REDIRECT BERDASARKAN PERAN ---

        // Cek jika peran user adalah 'admin'
        if ($user->role === 'admin') {
            // Arahkan ke path default Filament (biasanya '/admin')
            // Menggunakan config('filament.path') lebih aman daripada hardcode '/admin'
            return redirect()->intended(config('filament.path', '/admin'));
        }

        // Untuk peran lainnya ('user' atau default), arahkan ke halaman utama
        // Pastikan Anda punya rute bernama 'home' di routes/web.php
        return redirect()->intended(route('home'));

        // --- SELESAI LOGIKA REDIRECT ---
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
