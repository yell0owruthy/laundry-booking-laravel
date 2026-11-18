{{-- Menggunakan kerangka tampilan utama bawaan Laravel Breeze --}}
<x-app-layout>
    {{-- Memuat Tailwind CSS via CDN agar desain langsung tampil rapi --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Bagian judul atas halaman (Header) untuk Pelanggan/User --}}
    <x-slot name="header">
        {{-- Menampilkan judul halaman pemesanan bagi User --}}
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Pemesanan Laundry — Pilih Opsi Layanan & Kecepatan') }}
        </h2>
    </x-slot>

    {{-- Pembungkus utama konten halaman User --}}
    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

        {{-- Menampilkan pesan sukses jika pesanan baru saja berhasil dibuat --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg shadow-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif

        {{-- ============================================================== --}}
        {{-- BAGIAN 1: DAFTAR HARGA OPSI LAYANAN LAUNDRY                    --}}
        {{-- ============================================================== --}}
        <div class="bg-white p-6 rounded-xl shadow space-y-4">
            {{-- Judul daftar layanan yang tersedia --}}
            <h3 class="text-lg font-bold text-gray-800 border-b pb-2">Daftar Opsi Layanan Kami</h3>

            {{-- Menampilkan kartu-kartu layanan dalam bentuk grid 2 sampai 4 kolom --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Melakukan perulangan untuk menampilkan setiap layanan dari database --}}
                @foreach($services as $item)
                    <div class="border rounded-lg p-4 bg-blue-50/40 hover:shadow transition">
                        {{-- Nama layanan laundry --}}
                        <h4 class="font-bold text-gray-800 text-base">{{ $item->nama_layanan }}</h4>
                        {{-- Harga dasar per satuan (kg/pcs) --}}
                        <p class="text-blue-700 font-extrabold text-lg mt-1">
                            Rp {{ number_format($item->harga_dasar, 0, ',', '.') }}
                            <span class="text-xs font-normal text-gray-500">/ {{ $item->satuan }}</span>
                        </p>
                        {{-- Deskripsi singkat layanan --}}
                        <p class="text-xs text-gray-600 mt-2">{{ $item->deskripsi }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ============================================================== --}}
        {{-- BAGIAN 2: FORM PEMESANAN LAYANAN LAUNDRY                       --}}
        {{-- ============================================================== --}}
        <div class="bg-white p-6 rounded-xl shadow space-y-4">
            {{-- Judul form pemesanan --}}
            <h3 class="text-lg font-bold text-gray-800 border-b pb-2">Form Pemesanan Layanan Laundry</h3>

            {{-- Form pemesanan yang mengirim data ke rute user.order.store (Method POST) --}}
            <form action="{{ route('user.order.store') }}" method="POST" class="space-y-4">
                {{-- Token keamanan wajib Laravel --}}
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Input 1: Dropdown Pilih Jenis Layanan --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">1. Pilih Opsi Layanan</label>
                        <select name="service_id" required class="w-full border-gray-300 rounded-lg p-2.5 border text-sm">
                            <option value="">-- Pilih Layanan Laundry --</option>
                            {{-- Menampilkan seluruh opsi layanan beserta harga dasarnya --}}
                            @foreach($services as $service)
                                <option value="{{ $service->id }}">
                                    {{ $service->nama_layanan }} (Rp {{ number_format($service->harga_dasar, 0, ',', '.') }}/{{ $service->satuan }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Input 2: Dropdown Pilih Tipe Kecepatan (Reguler, Cepat, Lebih Cepat) --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">2. Pilih Kecepatan Pengerjaan</label>
                        <select name="tipe_kecepatan" required class="w-full border-gray-300 rounded-lg p-2.5 border text-sm">
                            {{-- Opsi Reguler tanpa tambahan biaya --}}
                            <option value="reguler">Reguler (Estimasi 2-3 Hari) — Tanpa Biaya Tambahan</option>
                            {{-- Opsi Cepat dengan tambahan Rp5.000 per kg/pcs --}}
                            <option value="cepat">Cepat (Estimasi 1 Hari / 24 Jam) — +Rp 5.000 / satuan</option>
                            {{-- Opsi Lebih Cepat (Express) dengan tambahan Rp10.000 per kg/pcs --}}
                            <option value="lebih_cepat">Lebih Cepat / Express (6 Jam) — +Rp 10.000 / satuan</option>
                        </select>
                    </div>

                    {{-- Input 3: Berat Cucian (kg) atau Jumlah Potong (pcs) --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">3. Berat (kg) / Jumlah (pcs)</label>
                        <input type="number" name="jumlah" min="1" value="1" required
                               class="w-full border-gray-300 rounded-lg p-2.5 border text-sm"
                               placeholder="Masukkan angka (contoh: 3)">
                    </div>
                </div>

                {{-- Input 4: Catatan Tambahan atau Alamat Penjemputan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">4. Catatan / Alamat (Opsional)</label>
                    <textarea name="catatan" rows="2" class="w-full border-gray-300 rounded-lg p-2.5 border text-sm"
                              placeholder="Contoh: Tolong jangan pakai pemutih, jemput di Jl. Melati No. 10..."></textarea>
                </div>

                {{-- Tombol Submit untuk mengirim pesanan --}}
                <div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2.5 rounded-lg text-sm shadow">
                        Buat Pesanan Laundry Sekarang
                    </button>
                </div>
            </form>
        </div>

        {{-- ============================================================== --}}
        {{-- BAGIAN 3: RIWAYAT PESANAN SAYA                                 --}}
        {{-- ============================================================== --}}
        <div class="bg-white p-6 rounded-xl shadow space-y-4">
            {{-- Judul tabel riwayat pesanan milik user yang sedang login --}}
            <h3 class="text-lg font-bold text-gray-800 border-b pb-2">Riwayat Pesanan Saya</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 border-b">
                            <th class="p-3">Tanggal Pesan</th>
                            <th class="p-3">Layanan</th>
                            <th class="p-3">Tipe Kecepatan</th>
                            <th class="p-3">Jumlah</th>
                            <th class="p-3">Total Bayar</th>
                            <th class="p-3">Catatan</th>
                            <th class="p-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        {{-- Menampilkan setiap riwayat pesanan milik user dari variabel $myTransactions --}}
                        @forelse($myTransactions as $order)
                            <tr class="hover:bg-gray-50">
                                {{-- Tanggal pemesanan --}}
                                <td class="p-3 text-gray-600">{{ $order->created_at->format('d M Y, H:i') }}</td>

                                {{-- Nama layanan yang dipesan --}}
                                <td class="p-3 font-semibold text-gray-800">{{ $order->service->nama_layanan ?? '-' }}</td>

                                {{-- Tipe kecepatan yang dipilih --}}
                                <td class="p-3 capitalize">
                                    {{ str_replace('_', ' ', $order->tipe_kecepatan) }}
                                    @if($order->biaya_tambahan_per_satuan > 0)
                                        <span class="text-xs text-gray-500">
                                            (+Rp {{ number_format($order->biaya_tambahan_per_satuan, 0, ',', '.') }}/satuan)
                                        </span>
                                    @endif
                                </td>

                                {{-- Jumlah berat atau potong --}}
                                <td class="p-3">{{ $order->jumlah }} {{ $order->service->satuan ?? 'kg' }}</td>

                                {{-- Total harga yang harus dibayar --}}
                                <td class="p-3 font-bold text-blue-700">
                                    Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                                </td>

                                {{-- Catatan pesanan --}}
                                <td class="p-3 text-gray-600">{{ $order->catatan ?: '-' }}</td>

                                {{-- Badge warna status pengerjaan laundry --}}
                                <td class="p-3">
                                    @if($order->status === 'pending')
                                        <span class="bg-amber-100 text-amber-800 px-2.5 py-1 rounded-full text-xs font-semibold">Menunggu (Pending)</span>
                                    @elseif($order->status === 'diproses')
                                        <span class="bg-blue-100 text-blue-800 px-2.5 py-1 rounded-full text-xs font-semibold">Sedang Diproses</span>
                                    @else
                                        <span class="bg-emerald-100 text-emerald-800 px-2.5 py-1 rounded-full text-xs font-semibold">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        {{-- Jika user belum pernah memesan sama sekali --}}
                        @empty
                            <tr>
                                <td colspan="7" class="p-4 text-center text-gray-500">Kamu belum memiliki riwayat pesanan laundry.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>