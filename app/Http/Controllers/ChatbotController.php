<?php

namespace App\Http\Controllers;

use App\Models\LogChatbot;
use App\Services\ChatbotStokService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatbotController extends Controller
{
    public function index(): View
    {
        return view('chatbot.index');
    }

    public function pesan(Request $request, ChatbotStokService $chatbot): JsonResponse
    {
        $data = $request->validate([
            'pesan' => ['required', 'string', 'max:255'],
        ], [
            'pesan.required' => 'Pesan tidak boleh kosong.',
            'pesan.max' => 'Pesan maksimal 255 karakter.',
        ]);

        return response()->json([
            'jawaban' => $chatbot->jawab($data['pesan'], $request->user()),
        ]);
    }

    public function log(): View
    {
        $log = LogChatbot::query()
            ->with(['user:id,name', 'produk:id,nama_produk'])
            ->latest('id')
            ->paginate(20);

        return view('chatbot.log', ['log' => $log]);
    }
}
