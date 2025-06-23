<div class="flex flex-col h-full">
    <div class="chat-header border-b border-gray-200 p-4 flex items-center justify-between bg-white sticky top-0 z-10">
        @php
            $isStoreOwner = auth()->id() === optional($chat->store)->user_id;
            $chatPartner = $isStoreOwner ? $chat->user : $chat->store;
            $chatPartnerName = $isStoreOwner
                ? ($chat->user->name ?? 'User')
                : ($chat->store->name ?? 'Toko');
            $chatPartnerPhoto = $isStoreOwner
                ? ($chat->user->profile_photo_url ?? null)
                : ($chat->store->logo_url ?? null);
        @endphp
        <div class="flex items-center gap-3">
            {{-- Tombol kembali di sini dihapus --}}
            <div class="w-10 h-10 rounded-full bg-sage-200 flex items-center justify-center overflow-hidden text-primary-700 font-bold">
                @if($chatPartnerPhoto)
                    <img src="{{ $chatPartnerPhoto }}" class="object-cover w-full h-full" alt="{{ $chatPartnerName }}">
                @else
                    {{ strtoupper(mb_substr($chatPartnerName, 0, 1)) }}
                @endif
            </div>
            <div class="flex flex-col">
                <span class="font-semibold text-gray-900">{{ $chatPartnerName }}</span>
                @if(!$isStoreOwner && $chat->store)
                    <span class="text-xs text-gray-500">Toko: {{ $chat->store->name }}</span>
                @elseif($isStoreOwner && $chat->user)
                    <span class="text-xs text-gray-500">Pembeli: {{ $chat->user->name }}</span>
                @endif
            </div>
        </div>
        {{-- Tombol aksi lainnya --}}
    </div>

    <div id="message-list" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
        @forelse($messages as $message)
            @php
                $isSender = $message->user_id === auth()->id();
                $messageClass = $isSender ? 'self-end bg-sage-500 text-white' : 'self-start bg-gray-200 text-gray-800';
                $borderRadius = $isSender ? 'rounded-tl-lg rounded-tr-lg rounded-bl-lg' : 'rounded-tl-lg rounded-tr-lg rounded-br-lg';
            @endphp
            <div class="flex {{ $isSender ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[70%] p-3 {{ $messageClass }} {{ $borderRadius }} shadow-sm">
                    <p class="text-sm">
                        {!! nl2br(e($message->message)) !!}
                    </p>
                    <span class="block text-right text-xs mt-1 {{ $isSender ? 'text-white' : 'text-gray-600' }} opacity-75">
                        {{ $message->created_at->format('H:i') }}
                    </span>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center h-full text-gray-500">
                <svg class="w-16 h-16 mb-4" fill="none" stroke="#a7c1a8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4-.84l-4 1 .86-3.08A7.953 7.953 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <div class="text-lg font-semibold text-sage-400">Mulai percakapan</div>
                <p class="text-sm text-gray-400">Ketik pesan pertama Anda di bawah.</p>
            </div>
        @endforelse
    </div>

    <div class="chat-input border-t border-gray-200 p-4 bg-white sticky bottom-0 z-10">
        <form id="message-form" class="flex gap-2" onsubmit="sendMessage(event)">
            @csrf
            <input type="hidden" name="chat_id" value="{{ $chat->id }}">
            <textarea
                name="message"
                rows="1"
                placeholder="Ketik pesan..."
                class="flex-1 rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring focus:ring-sage-200 focus:border-sage-400 resize-none overflow-hidden"
                oninput="autoExpand(this)"
                required
            ></textarea>
            <button type="submit" class="bg-sage-600 text-white px-4 py-2 rounded-lg hover:bg-sage-700 transition-colors flex items-center justify-center">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                <span class="sr-only">Kirim</span>
            </button>
        </form>
    </div>
</div>