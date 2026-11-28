<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
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

        // Login ini hanya diperuntukkan bagi pemilik dan pekerja.
        if (!in_array($request->user()->role, [
            User::ROLE_PEMILIK,
            User::ROLE_PEKERJA
        ], true)) {

            Auth::logout();

            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => 'Akun pelanggan harus menggunakan halaman login pelanggan.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $role = $request->user()?->role;

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Mengarahkan pengguna ke halaman login sesuai role.
        if ($role === User::ROLE_PELANGGAN) {
            return redirect()->route('pelanggan.login');
        }

        return redirect()->route('login');
    }
}
