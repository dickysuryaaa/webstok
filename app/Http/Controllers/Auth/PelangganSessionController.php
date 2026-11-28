<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PelangganSessionController extends Controller
{
    /**
     * Menampilkan halaman login pelanggan.
     */
    public function create(): View
    {
        return view('auth.pelanggan.login');
    }

    /**
     * Memproses login pelanggan.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials['role'] = User::ROLE_PELANGGAN;

        if (!Auth::attempt(
            $credentials,
            $request->boolean('remember')
        )) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password pelanggan tidak sesuai.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}