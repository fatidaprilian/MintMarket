<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function getOrCreateChat(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
        ]);

        $userId = Auth::id();
        $storeId = $request->store_id;
        $messageText = $request->message;

        $chat = Chat::firstOrCreate(
            ['user_id' => $userId, 'store_id' => $storeId]
        );

        if ($messageText) {
            // Periksa apakah pesan awal sudah ada untuk chat ini dari pengguna ini
            $existingMsg = Message::where('chat_id', $chat->id)
                ->where('user_id', $userId)
                ->where('message', $messageText) // Tambahkan kondisi pesan untuk menghindari duplikasi pesan inisial yang sama
                ->first();

            // Jika belum ada pesan ini, buat pesan baru
            if (!$existingMsg) {
                Message::create([
                    'chat_id' => $chat->id,
                    'user_id' => $userId,
                    'message' => $messageText,
                ]);
            }
        }

        if (!$request->wantsJson()) {
            // Perubahan di sini: Redirect selalu ke daftar chat utama tanpa ID chat
            return redirect()->route('chat.userChats');
        }

        return response()->json([
            'status' => 'success',
            'chat' => $chat,
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|exists:chats,id',
            'message' => 'required|string',
        ]);

        $chat = Chat::findOrFail($request->chat_id);

        $userId = Auth::id();
        if ($chat->user_id !== $userId && optional($chat->store)->user_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $message = Message::create([
            'chat_id' => $chat->id,
            'user_id' => $userId,
            'message' => $request->message,
        ]);

        \Log::info('Message sent', [
            'chat_id' => $chat->id,
            'user_id' => $userId,
            'message_id' => $message->id,
        ]);

        // Karena kita menggunakan AJAX untuk mengirim pesan dan kemudian me-render ulang partial,
        // kita ingin controller ini selalu mengembalikan partial yang diperbarui.
        // Ini memastikan tampilan obrolan segera diperbarui dengan pesan baru.
        $chat = Chat::with(['user', 'store', 'messages.user'])->findOrFail($chat->id);
        $messages = $chat->messages()->with('user')->orderBy('created_at')->get();
        return view('chat.partials.messages', compact('chat', 'messages'))->render();
    }

    public function messages(Request $request, $chat_id)
    {
        $chat = Chat::with(['user', 'store', 'messages.user'])->findOrFail($chat_id);
        $userId = Auth::id();

        if ($chat->user_id !== $userId && optional($chat->store)->user_id !== $userId) {
            abort(403, 'Unauthorized');
        }

        $messages = $chat->messages()->with('user')->orderBy('created_at')->get();

        \Log::info('Chat messages loaded', [
            'chat_id' => $chat->id,
            'user_id' => $userId,
            'message_ids' => $messages->pluck('id')->toArray(),
            'message_count' => $messages->count(),
        ]);

        // Selalu kembalikan partial view
        return view('chat.partials.messages', compact('chat', 'messages'))->render();
    }

    public function userChats(Request $request)
    {
        $userId = Auth::id();

        $chats = Chat::with(['user', 'store', 'messages.user'])
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->orWhereHas('store', function ($q2) use ($userId) {
                        $q2->where('user_id', $userId);
                    });
            })
            ->latest()
            ->get()
            ->unique('id')
            ->values();

        \Log::info('User chats loaded', [
            'user_id' => $userId,
            'chat_ids' => $chats->pluck('id')->toArray(),
            'chat_count' => $chats->count(),
        ]);

        if (!$request->wantsJson()) {
            return view('chat.index', [
                'chats' => $chats,
                'activeChatId' => $request->chat, // Tetap gunakan ini untuk inisialisasi jika datang dari klik chat di sidebar
            ]);
        }

        return response()->json([
            'status' => 'success',
            'chats' => $chats,
        ]);
    }
}
