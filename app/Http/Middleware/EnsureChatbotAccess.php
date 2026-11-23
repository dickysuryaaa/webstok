<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Chatbot terbuka untuk tamu hanya bila config('toko.chatbot_public') true.
 * Dicek saat request (bukan saat rute didaftarkan) agar mengikuti konfigurasi aktif.
 */
class EnsureChatbotAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('toko.chatbot_public') && ! $request->user()) {
            throw new AuthenticationException('Unauthenticated.', ['web']);
        }

        return $next($request);
    }
}
