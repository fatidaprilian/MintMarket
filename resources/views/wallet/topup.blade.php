@extends('layouts.app')
@section('title', 'Top Up Wallet')

@section('content')
<div class="max-w-xl mx-auto py-12 px-4">
    <h1 class="text-2xl font-bold mb-8 text-emerald-700">Top Up Wallet</h1>
    {{-- Notifikasi error --}}
    @if($errors->any())
        <div class="mb-4 p-3 bg-red-100 border border-red-200 rounded text-red-700">
            <ul class="pl-4 list-disc">
                @foreach($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Top Up --}}
    <form id="topupForm" action="{{ route('wallet.topup.submit') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow rounded-2xl p-7 ring-1 ring-emerald-100">
        @csrf
        <div class="mb-6">
            <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Nominal Top Up</label>
            <input type="number" min="10000" step="1000" name="amount" id="amount"
                class="block w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-emerald-400 focus:border-emerald-400"
                placeholder="Masukkan nominal, contoh: 100000" required>
            <span class="text-xs text-gray-500 mt-1 block">Minimal top up Rp 10.000</span>
        </div>
        <div class="mb-6">
            <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
            <select name="payment_method" id="payment_method" class="block w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-emerald-400 focus:border-emerald-400" required>
                <option value="" disabled selected>Pilih Metode</option>
                <option value="bca">Transfer Bank BCA</option>
                <option value="bri">Transfer Bank BRI</option>
                <option value="gopay">GoPay</option>
                <option value="dana">DANA</option>
                <option value="ovo">OVO</option>
            </select>
        </div>
        <div id="proofUpload" class="mb-6 hidden">
            <label for="proof" class="block text-sm font-medium text-gray-700 mb-1">Upload Bukti Pembayaran</label>
            <input type="file" name="proof" id="proof" accept="image/*" class="block w-full text-sm text-gray-600">
            <span class="text-xs text-gray-500 mt-1 block">Format gambar (.jpg, .jpeg, .png). Maksimal 2MB.</span>
        </div>
        <button type="button" id="btnBayar" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 rounded-lg text-lg shadow transition-all">
            Lanjutkan &rarr;
        </button>
    </form>
</div>

{{-- Modal Pembayaran --}}
<div id="modalPembayaran" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-8 relative animate-fade-in">
        <button type="button" class="absolute top-3 right-3 text-gray-400 hover:text-red-500" onclick="closeModal()">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <h2 class="text-xl font-bold mb-3 text-emerald-700 flex items-center gap-2">
            <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke="currentColor"/>
                <path d="M12 8v4l3 3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Instruksi Pembayaran
        </h2>
        <div id="detailPembayaran" class="mb-4">
            <!-- Akan diisi oleh JS -->
        </div>
        <div id="uploadProofModal" class="mb-4 hidden">
            <label for="modal_proof" class="block text-sm font-medium text-gray-700 mb-1">Upload Bukti Pembayaran</label>
            <input type="file" name="modal_proof" id="modal_proof" accept="image/*" class="block w-full text-sm text-gray-600">
            <span class="text-xs text-gray-500 mt-1 block">Format gambar (.jpg, .jpeg, .png). Maksimal 2MB.</span>
        </div>
        <button type="button" onclick="submitTopup()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 rounded-lg text-lg shadow transition-all">
            Saya Sudah Bayar
        </button>
    </div>
</div>

{{-- Script --}}
@push('scripts')
<script>
    const paymentData = {
        bca: {
            title: "Transfer Bank BCA",
            norek: "1234567890",
            nama: "PT MintMarket Indonesia",
            instruksi: "Silakan transfer ke rekening berikut dan upload bukti transfer.",
            logo: "https://upload.wikimedia.org/wikipedia/commons/5/5f/Bank_Central_Asia_logo.svg"
        },
        bri: {
            title: "Transfer Bank BRI",
            norek: "0987654321",
            nama: "PT MintMarket Indonesia",
            instruksi: "Silakan transfer ke rekening berikut dan upload bukti transfer.",
            logo: "https://upload.wikimedia.org/wikipedia/commons/7/7e/Bank_Rakyat_Indonesia_logo.svg"
        },
        gopay: {
            title: "GoPay",
            norek: "081234567890",
            nama: "MintMarket",
            instruksi: "Silakan transfer ke nomor GoPay berikut dan upload bukti pembayaran.",
            logo: "https://upload.wikimedia.org/wikipedia/commons/3/3d/Logo_gopay.svg"
        },
        dana: {
            title: "DANA",
            norek: "081234567891",
            nama: "MintMarket",
            instruksi: "Silakan transfer ke nomor DANA berikut dan upload bukti pembayaran.",
            logo: "https://upload.wikimedia.org/wikipedia/commons/7/7e/Logo_dana_blue.svg"
        },
        ovo: {
            title: "OVO",
            norek: "081234567892",
            nama: "MintMarket",
            instruksi: "Silakan transfer ke nomor OVO berikut dan upload bukti pembayaran.",
            logo: "https://upload.wikimedia.org/wikipedia/commons/1/16/Logo_OVO_purple.svg"
        }
    };

    const btnBayar = document.getElementById('btnBayar');
    const modal = document.getElementById('modalPembayaran');
    const detailPembayaran = document.getElementById('detailPembayaran');
    const paymentMethod = document.getElementById('payment_method');
    const uploadProofModal = document.getElementById('uploadProofModal');
    const proofUpload = document.getElementById('proofUpload');

    btnBayar.addEventListener('click', function(e) {
        e.preventDefault();
        const method = paymentMethod.value;
        const amount = document.getElementById('amount').value;

        if(!method || !amount || amount < 10000) {
            alert('Lengkapi nominal dan metode pembayaran!');
            return;
        }

        // Show modal
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Fill payment detail
        const data = paymentData[method];
        detailPembayaran.innerHTML = `
            <div class="flex items-center gap-3 mb-3">
                <img src="${data.logo}" alt="${data.title}" class="w-12 h-12 rounded bg-gray-50 border p-1">
                <div>
                    <div class="text-base font-bold text-emerald-700">${data.title}</div>
                    <div class="text-xs text-gray-400">MintMarket Payment</div>
                </div>
            </div>
            <div class="mb-2">
                <div class="font-semibold text-gray-800">Nominal:</div>
                <div class="text-lg text-emerald-600 font-bold mb-1">Rp ${parseInt(amount).toLocaleString('id-ID')}</div>
            </div>
            <div class="mb-2">
                <div class="font-semibold text-gray-800">No. Rekening / No. HP:</div>
                <div class="text-lg tracking-wider">${data.norek}</div>
            </div>
            <div class="mb-2">
                <div class="font-semibold text-gray-800">Nama Penerima:</div>
                <div class="text-md">${data.nama}</div>
            </div>
            <div class="mb-2 text-gray-600">${data.instruksi}</div>
        `;

        // Show proof upload in modal, hide in form
        proofUpload.classList.add('hidden');
        uploadProofModal.classList.remove('hidden');
    });

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Submit form with proof from modal
    function submitTopup() {
        // Move file from modal input to main form input
        let modalProofInput = document.getElementById('modal_proof');
        let formProofInput = document.getElementById('proof');
        if(modalProofInput.files.length > 0) {
            // Create a DataTransfer to move file
            let dt = new DataTransfer();
            dt.items.add(modalProofInput.files[0]);
            formProofInput.files = dt.files;
        }
        // Submit form
        document.getElementById('topupForm').submit();
    }
</script>
@endpush
@endsection