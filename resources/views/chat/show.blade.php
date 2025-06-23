@extends('layouts.app')

@section('title', 'Chat - ' . ($chat->store->name ?? $chat->user->name))

@push('styles')
<style>
    .messages-scroll {
        max-height: 60vh;
        min-height: 300px;
        overflow-y: auto;
        scroll-behavior: smooth;
    }
    .bubble-left {
        background: theme('colors.sage.100');
        color: theme('colors.sage.900');
    }
    .bubble-right {
        background: theme('colors.primary.500');
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="bg-white border rounded-xl shadow-md">
        <!-- Header -->
        <div class="flex items-center gap-3 border-b p-4 bg-sage-50 rounded-t-xl">
            <div class="w-12 h-12 rounded-full overflow-hidden bg-sage-200 flex items-center justify-center font-bold text-xl text-primary-600">
                @if($chat->store && $chat->store->logo_url)
                    <img src="{{ $chat->store->logo_url }}" alt="logo" class="object-cover w-full h-full">
                @elseif($chat->user && $chat->user->profile_photo_url)
                    <img src="{{ $chat->user->profile_photo_url }}" alt="user" class="object-cover w-full h-full">
                @else
                    {{ strtoupper(mb_substr($chat->store->name ?? $chat->user->name ?? 'U', 0, 1)) }}
                @endif
            </div>
            <div>
                <div class="font-semibold text-lg text-sage-900">
                    {{ $chat->store->name ?? $chat->user->name }}
                </div>
                @if($chat->store)
                    <div class="text-xs text-sage-600">Toko</div>
                @else
                    <div class="text-xs text-sage-600">User</div>
                @endif
            </div>
        </div>

        <!-- Messages -->
        <div class="messages-scroll bg-sage-50 px-4 py-6 space-y-6" id="message-list">
            @foreach($messages as $msg)
                @php
                    $isMe = $msg->user_id === auth()->id();
                @endphp
                <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-xs md:max-w-md flex flex-col items-{{ $isMe ? 'end' : 'start' }}">
                        <div class="flex items-center gap-2 mb-1">
                            @if(!$isMe)
                                <div class="w-7 h-7 rounded-full overflow-hidden bg-sage-200 flex items-center justify-center text-xs font-bold text-sage-700">
                                    @if($msg->user && $msg->user->profile_photo_url)
                                        <img src="{{ $msg->user->profile_photo_url }}" alt="user" class="object-cover w-full h-full">
                                    @else
                                        {{ strtoupper(mb_substr($msg->user->name ?? 'U', 0, 1)) }}
                                    @endif
                                </div>
                            @endif
                            <span class="text-xs text-sage-600">
                                {{ $msg->user->name ?? 'User' }}
                            </span>
                        </div>
                        <div class="rounded-2xl px-4 py-2 text-sm shadow 
                            {{ $isMe 
                                ? 'bubble-right bg-primary-500 text-white rounded-br-sm' 
                                : 'bubble-left bg-sage-100 text-sage-900 rounded-bl-sm' 
                            }}">
                            {!! nl2br(e($msg->message)) !!}
                        </div>
                        <div class="mt-1 text-[11px] text-sage-500">
                            {{ $msg->created_at->format('H:i, d M Y') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Form Kirim Pesan -->
        <form method="POST" action="{{ route('chat.send') }}" class="flex gap-3 border-t p-4 bg-sage-50">
            @csrf
            <input type="hidden" name="chat_id" value="{{ $chat->id }}">
            <input
                type="text"
                name="message"
                class="flex-1 rounded-lg border-gray-300 focus:ring-primary-300 focus:border-primary-400 px-4 py-2 text-sm font-medium bg-white"
                placeholder="Ketik pesan..."
                required
                autocomplete="off"
            >
            <button type="submit" class="px-6 py-2 rounded-lg font-bold bg-primary-600 text-white hover:bg-primary-700 transition shadow">
                Kirim
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Scroll ke bawah otomatis pada pesan baru
    window.onload = function() {
        const msgList = document.getElementById('message-list');
        if (msgList) msgList.scrollTop = msgList.scrollHeight;
    };
</script>
@endpush
@endsection