<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Judul tab browser menampilkan nomor nota transaksi --}}
    <title>Struk Laundry #{{ $transaction->id }}</title>
    {{-- Memuat Tailwind CSS via CDN agar tampilan struk rapi --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Pengaturan khusus saat tombol Cetak (Print) ditekan */
        @media print {
            /* Menyembunyikan tombol Cetak dan Kembali agar tidak ikut tercetak di kertas */
            .no-print { display: none !important; }
            /* Menghilangkan bayangan dan warna latar belakang halaman saat dicetak */
            body { background: white !important; }
        }
    </style>
</head>
<body class="bg-gray-100 py-10 px-4">

    {{-- Kotak kertas struk berukuran lebar 380px (mirip ukuran kertas struk kasir) --}}
    <div class="max-w-sm mx-auto bg-white p-6 rounded-lg shadow-md border border-gray-200 font-mono text-sm">

        {{-- Kepala Struk (Nama Toko Laundry) --}}
        <div class="text-center border-b border-dashed border-gray-400 pb-4 mb-4">
            <h1 class="text-lg font-bold uppercase tracking-wider">Laundry Bersih Jaya</h1>
            <p class="text-xs text-gray-500 mt-1">Struk Bukti Pemesanan Resmi</p>
            <p class="text-xs text-gray-500">No. Nota: #TRX-{{ str_pad($transaction->id, 4, '0', STR_PAD_LEFT) }}</p>
        </div>

        {{-- Informasi Pelanggan & Waktu Transaksi --}}
        <div class="space-y-1.5 text-xs border-b border-dashed border-gray-400 pb-4 mb-4">
            <div class="flex justify-between">
                <span class="text-gray-500">Tanggal:</span>
                <span class="font-semibold">{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Pelanggan:</span>
                <span class="font-semibold">{{ $transaction->user->name ?? '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Status:</span>
                <span class="uppercase font-bold">{{ $transaction->status }}</span>
            </div>
        </div>

        {{-- Rincian Layanan & Kecepatan --}}
        <div class="space-y-2 text-xs border-b border-dashed border-gray-400 pb-4 mb-4">
            <div>
                <p class="font-bold text-gray-800">{{ $transaction->service->nama_layanan ?? '-' }}</p>
                <p class="text-gray-600 capitalize">
                    Kecepatan: {{ str_replace('_', ' ', $transaction->tipe_kecepatan) }}
                </p>
            </div>
            <div class="flex justify-between">
                <span>Harga Dasar:</span>
                <span>Rp {{ number_format($transaction->service->harga_dasar ?? 0, 0, ',', '.') }} / {{ $transaction->service->satuan ?? 'kg' }}</span>
            </div>
            <div class="flex justify-between">
                <span>Biaya Kecepatan:</span>
                <span>+Rp {{ number_format($transaction->biaya_tambahan_per_satuan, 0, ',', '.') }} / {{ $transaction->service->satuan ?? 'kg' }}</span>
            </div>
            <div class="flex justify-between">
                <span>Jumlah / Berat:</span>
                <span>{{ $transaction->jumlah }} {{ $transaction->service->satuan ?? 'kg' }}</span>
            </div>
            @if($transaction->catatan)
                <div class="pt-1 text-gray-600">
                    <span>Catatan:</span> {{ $transaction->catatan }}
                </div>
            @endif
        </div>

        {{-- Total Pembayaran --}}
        <div class="flex justify-between items-center text-base font-bold border-b border-dashed border-gray-400 pb-4 mb-4">
            <span>TOTAL BAYAR</span>
            <span>Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</span>
        </div>

        {{-- Pesan Penutup Struk --}}
        <p class="text-center text-xs text-gray-500 mb-5">
            Terima kasih telah mempercayakan cucian Anda kepada kami!
        </p>

        {{-- Tombol Aksi (Otomatis hilang saat mode Print) --}}
        <div class="no-print flex gap-2">
            {{-- Tombol memanggil dialog cetak / simpan PDF bawaan browser --}}
            <button onclick="window.print()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-sans font-semibold py-2 rounded text-xs">
                Cetak / Simpan PDF
            </button>
            {{-- Tombol kembali ke halaman sebelumnya --}}
            <a href="{{ url()->previous() }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-sans font-semibold py-2 px-4 rounded text-xs text-center">
                Kembali
            </a>
        </div>

    </div>

</body>
</html>