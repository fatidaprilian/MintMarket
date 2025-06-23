@extends('layouts.app')
@section('hide_search_bar', true)

@section('title', 'Chat Masuk')

@push('styles')
<style>
    .chat-layout {
        display: flex;
        /* Contoh: tinggi viewport - tinggi nav (64px) - padding atas/bawah (2 * 1rem) */
        height: calc(100vh - 4rem - 3rem);
        min-height: 500px; /* Tinggi minimum agar tidak terlalu kecil di desktop */
    }

    .chat-sidebar {
        width: 350px; /* Lebar sidebar default */
        flex-shrink: 0;
        display: flex;
        flex-direction: column; /* Ubah ke column untuk flexbox vertikal */
        border-right: 1px solid #e5e7eb; /* border-gray-200 */
    }

    .chat-sidebar > div:first-child { /* Konten atas sidebar (pencarian & daftar chat) */
        flex-grow: 1; /* Biarkan konten ini mengisi ruang yang tersedia */
        display: flex; /* Aktifkan flexbox untuk konten ini */
        flex-direction: column; /* Susun item secara vertikal */
        overflow: hidden; /* Penting untuk mengelola overflow daftar chat */
    }

    .chat-sidebar .flex-1.overflow-y-auto { /* Daftar chat scrollable */
        flex-grow: 1;
        overflow-y: auto;
    }


    .chat-main {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        height: 100%;
        position: relative; /* Untuk sticky header/footer */
    }

    .chat-list-item {
        cursor: pointer;
        transition: background-color 0.2s ease-in-out;
    }
    .chat-list-item:hover {
        background-color: #f3f4f6; /* gray-100 */
    }
    .chat-list-item.active {
        background-color: #e0f2e0; /* sage-100 */
        border-left: 4px solid #4ade80; /* emerald-400, bisa disesuaikan dengan sage-600 */
        padding-left: calc(1.25rem - 4px); /* Sesuaikan padding agar tidak bergeser */
    }
    .chat-list-item.active .unread-dot {
        display: none; /* Sembunyikan dot unread jika chat aktif */
    }

    .unread-dot {
        width: 8px;
        height: 8px;
        background-color: #ef4444; /* red-500 */
        border-radius: 50%;
        margin-left: 8px;
    }

    .sidebar-bottom {
        padding: 1rem;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-start;
        flex-shrink: 0; /* Pastikan tidak menyusut */
    }
    .back-btn {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        background-color: #f3f4f6;
        border-radius: 0.5rem;
        color: #4b5563;
        font-size: 0.875rem;
        font-weight: 500;
        transition: background-color 0.2s ease-in-out;
    }
    .back-btn:hover {
        background-color: #e5e7eb;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .chat-layout {
            flex-direction: column;
            height: calc(100vh - 4rem); /* Full height on mobile (adjust for nav) */
            min-height: auto;
        }
        .chat-sidebar {
            width: 100%;
            height: 100%; /* Default mobile view shows sidebar */
            border-right: none;
            border-bottom: 1px solid #e5e7eb;
        }
        .chat-main {
            position: absolute; /* Take full screen when active */
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: white;
            z-index: 20; /* Lebih tinggi dari sidebar */
            transform: translateX(100%); /* Sembunyikan secara default */
            transition: transform 0.3s ease-in-out;
        }
        .chat-main.active {
            transform: translateX(0); /* Tampilkan saat aktif */
        }
    }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <div class="flex chat-layout rounded-xl overflow-hidden border border-gray-200 shadow-md bg-white">
        <div class="chat-sidebar">
            {{-- Bagian atas sidebar (search & daftar chat) --}}
            <div> {{-- Ini akan mengisi ruang yang tersisa di sidebar --}}
                <div class="p-4 border-b">
                    <form method="GET" class="flex gap-2" autocomplete="off">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama user/toko..." class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:ring focus:ring-primary-200 focus:border-primary-400 w-full" />
                    </form>
                </div>
                <div class="flex-1 overflow-y-auto">
                    @if($chats->isEmpty())
                        <div class="flex flex-col items-center justify-center py-12 px-4 text-gray-500">
                            <img src="https://cdn-icons-png.flaticon.com/512/9068/9068753.png" class="w-16 h-16 mb-4" alt="No chat" loading="lazy">
                            <div>Belum ada chat masuk.</div>
                        </div>
                    @else
                        <ul>
                            @foreach($chats as $chat)
                                @php
                                    $isStoreOwner = auth()->id() === optional($chat->store)->user_id;
                                    $displayName = $isStoreOwner
                                        ? ($chat->user->name ?? 'User')
                                        : ($chat->store->name ?? 'Toko');
                                    $displayPhoto = $isStoreOwner
                                        ? ($chat->user->profile_photo_url ?? null)
                                        : ($chat->store->logo_url ?? null);
                                    $lastMsg = $chat->messages->last();
                                    $isUnread = $lastMsg && $lastMsg->user_id !== auth()->id() && !$lastMsg->is_read;
                                @endphp
                                <li
                                    class="chat-list-item flex items-center gap-3 px-5 py-4 border-b border-gray-200 {{ (isset($activeChatId) && $activeChatId == $chat->id) ? 'active' : '' }}"
                                    data-chat-id="{{ $chat->id }}"
                                    onclick="selectChat({{ $chat->id }}, this)"
                                >
                                    <div class="w-10 h-10 rounded-full bg-sage-200 flex items-center justify-center overflow-hidden text-primary-700 font-bold">
                                        @if($displayPhoto)
                                            <img src="{{ $displayPhoto }}" class="object-cover w-full h-full" alt="">
                                        @else
                                            {{ strtoupper(mb_substr($displayName, 0, 1)) }}
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium text-gray-900">{{ $displayName }}</span>
                                            @if($isUnread)
                                                <span class="unread-dot"></span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500 truncate w-44">
                                            @if($lastMsg)
                                                {{ mb_strimwidth(strip_tags($lastMsg->message), 0, 40, "...") }}
                                            @else
                                                <span class="italic text-gray-400">Belum ada pesan</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end min-w-[55px] gap-1">
                                        @if($lastMsg)
                                            <span class="text-[10px] text-gray-400">{{ $lastMsg->created_at->diffForHumans() }}</span>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
            {{-- Tombol Kembali dipindahkan ke sini --}}
            <div class="sidebar-bottom">
                <button type="button" class="back-btn" onclick="window.history.back()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 -ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali
                </button>
            </div>
        </div>

        <div class="chat-main" id="chat-content">
            <div class="flex-1 flex flex-col justify-center items-center text-gray-400 select-none h-full">
                <svg class="w-16 h-16 mb-2" fill="none" stroke="#a7c1a8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4-.84l-4 1 .86-3.08A7.953 7.953 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <div class="text-lg font-semibold text-sage-400">Pilih chat untuk mulai percakapan</div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let activeChatId = {{ isset($activeChatId) && $activeChatId ? $activeChatId : 'null' }};
let isMobile = window.matchMedia("(max-width: 768px)").matches;

// Fungsi untuk auto-expand textarea
function autoExpand(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = (textarea.scrollHeight) + 'px';
}

// Fungsi untuk mengirim pesan
function sendMessage(event) {
    event.preventDefault(); // Mencegah form submit secara default

    const form = event.target;
    const formData = new FormData(form);
    const messageInput = form.querySelector('textarea[name="message"]');

    if (messageInput.value.trim() === '') {
        return; // Jangan kirim pesan kosong
    }

    fetch('/chat/send', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest' // Penting untuk deteksi AJAX di Laravel
        },
        body: formData
    })
    .then(response => {
        // Periksa apakah respons adalah JSON atau HTML
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.indexOf("application/json") !== -1) {
            return response.json(); // Jika JSON, parse sebagai JSON
        } else {
            return response.text(); // Jika HTML, parse sebagai teks
        }
    })
    .then(data => {
        if (typeof data === 'string') { // Jika respons adalah HTML (dari .render() di controller)
            const chatContent = document.getElementById('chat-content');
            chatContent.innerHTML = data; // Perbarui seluruh konten chat-main
            initializeChatContent(); // Panggil fungsi inisialisasi setelah HTML baru dimasukkan
            // messageInput sudah dikosongkan dan direset di initializeChatContent
        } else { // Jika respons adalah JSON (opsional, jika sendMessage juga mengembalikan JSON)
            console.log('Message sent via JSON response:', data);
            // Di sini Anda mungkin ingin menambahkan pesan baru ke DOM secara dinamis
            // daripada memuat ulang seluruh partial.
        }
    })
    .catch(error => {
        console.error('Error sending message:', error);
        alert('Gagal mengirim pesan.');
    });
}

// Fungsi untuk menggulir ke bawah daftar pesan
function scrollToBottom() {
    const messageList = document.getElementById('message-list');
    if (messageList) {
        messageList.scrollTop = messageList.scrollHeight;
    }
}

// Fungsi yang dipanggil setelah chatContent di-update untuk inisialisasi elemen-elemen baru
function initializeChatContent() {
    scrollToBottom();
    // Inisialisasi autoExpand untuk semua textarea yang ada di konten chat
    const newTextarea = document.querySelector('#chat-content textarea[name="message"]');
    if (newTextarea) {
        newTextarea.value = ''; // Kosongkan input
        autoExpand(newTextarea); // Reset tinggi textarea
    }
}


function selectChat(chatId, listItem = null) {
    if(activeChatId === chatId && !isMobile) return;
    activeChatId = chatId;

    document.querySelectorAll('.chat-list-item').forEach(item => {
        item.classList.remove('active');
    });
    if(listItem) listItem.classList.add('active');
    else {
        let el = document.querySelector('.chat-list-item[data-chat-id="'+chatId+'"]');
        if(el) el.classList.add('active');
    }

    const chatContent = document.getElementById('chat-content');
    chatContent.innerHTML = '<div class="flex-1 flex flex-col justify-center items-center text-gray-400 py-12"><span class="mb-2 animate-pulse">Loading...</span></div>';

    fetch(`/chat/${chatId}/messages`)
        .then(res => res.text())
        .then(html => {
            chatContent.innerHTML = html;
            // Panggil fungsi inisialisasi setelah HTML baru dimasukkan
            initializeChatContent();

            // Aktifkan tampilan chat-main untuk mobile
            if (isMobile) {
                chatContent.classList.add('active');
                document.querySelector('.chat-sidebar').style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error loading messages:', error);
            chatContent.innerHTML = '<div class="flex-1 flex flex-col justify-center items-center text-red-400 py-12">Gagal memuat pesan.</div>';
        });
}

// Fungsi untuk menyembunyikan chat-main di mobile (kembali ke daftar chat)
function hideChatMain() {
    const chatContent = document.getElementById('chat-content');
    chatContent.classList.remove('active');
    document.querySelector('.chat-sidebar').style.display = 'flex';
    activeChatId = null;
}

document.addEventListener('DOMContentLoaded', function() {
    let chatToOpen = activeChatId;
    if (!chatToOpen) {
        const firstChat = document.querySelector('.chat-list-item');
        if(firstChat) chatToOpen = Number(firstChat.getAttribute('data-chat-id'));
    }
    if(chatToOpen) {
        selectChat(Number(chatToOpen));
    } else if (isMobile) {
        document.querySelector('.chat-sidebar').style.display = 'flex';
        document.getElementById('chat-content').classList.remove('active');
    }
});

// Listener untuk perubahan ukuran layar (misal: rotasi mobile)
window.addEventListener('resize', () => {
    const wasMobile = isMobile;
    isMobile = window.matchMedia("(max-width: 768px)").matches;
    if (wasMobile !== isMobile) {
        const sidebar = document.querySelector('.chat-sidebar');
        const chatMain = document.getElementById('chat-content');
        if (isMobile) {
            if (!activeChatId) {
                chatMain.classList.remove('active');
                sidebar.style.display = 'flex';
            } else {
                sidebar.style.display = 'none';
                chatMain.classList.add('active');
            }
        } else {
            sidebar.style.display = 'flex';
            chatMain.classList.remove('active');
            chatMain.style.transform = 'translateX(0)';
        }
    }
});
</script>
@endpush

@endsection