{{-- Menggunakan kerangka tampilan utama (navbar & tombol logout) bawaan Laravel Breeze --}}
<x-app-layout>
    {{-- Memuat Tailwind CSS via CDN agar tampilan tetap rapi meski lupa menjalankan npm run dev --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Bagian judul atas halaman (Header) --}}
    <x-slot name="header">
        {{-- Menampilkan teks judul halaman khusus Admin --}}
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Admin — Kelola Layanan & Rekap Penjualan Laundry') }}
        </h2>
    </x-slot>

    {{-- Pembungkus utama konten halaman dengan jarak atas-bawah (py-8) dan jarak antar seksi (space-y-8) --}}
    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

        {{-- Mengecek apakah ada pesan sukses dari Controller (misal setelah tambah/edit/hapus layanan) --}}
        @if(session('success'))
            {{-- Kotak notifikasi berwarna hijau jika proses berhasil --}}
            <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg shadow-sm">
                {{-- Menampilkan isi teks pesan sukses dari session --}}
                {{ session('success') }}
            </div>
        @endif

        {{-- ============================================================== --}}
        {{-- BAGIAN 1: KARTU RINGKASAN REKAP PENJUALAN                      --}}
        {{-- ============================================================== --}}
        {{-- Grid 2 kolom di layar sedang/besar (md:grid-cols-2) dan 1 kolom di HP --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Kartu 1: Menampilkan Total Pendapatan Penjualan (otomatis ikut berubah sesuai Filter) --}}
            <div class="bg-white p-6 rounded-xl shadow border-l-4 border-blue-600">
                {{-- Label judul kartu pendapatan --}}
                <p class="text-sm font-medium text-gray-500 uppercase">Total Pendapatan Penjualan</p>
                {{-- Menampilkan angka totalPendapatan dengan format Rupiah (titik ribuan) --}}
                <p class="text-3xl font-extrabold text-blue-700 mt-2">
                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                </p>
            </div>

            {{-- Kartu 2: Menampilkan Total Transaksi Masuk (otomatis ikut berubah sesuai Filter) --}}
            <div class="bg-white p-6 rounded-xl shadow border-l-4 border-emerald-600">
                {{-- Label judul kartu jumlah pesanan --}}
                <p class="text-sm font-medium text-gray-500 uppercase">Total Transaksi Masuk</p>
                {{-- Menampilkan jumlah transaksi dari variabel $totalTransaksi --}}
                <p class="text-3xl font-extrabold text-emerald-700 mt-2">
                    {{ $totalTransaksi }} Pesanan
                </p>
            </div>
        </div>

        {{-- ============================================================== --}}
        {{-- BAGIAN 2: KELOLA LAYANAN LAUNDRY (TAMBAH, EDIT, HAPUS)         --}}
        {{-- ============================================================== --}}
        <div class="bg-white p-6 rounded-xl shadow space-y-6">
            {{-- Judul seksi Kelola Layanan --}}
            <h3 class="text-lg font-bold text-gray-800 border-b pb-2">1. Kelola Layanan Laundry</h3>

            {{-- Form untuk menambahkan layanan laundry baru (mengirim data ke rute admin.services.store) --}}
            <form action="{{ route('admin.services.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-gray-50 p-4 rounded-lg border">
                {{-- Token keamanan wajib di setiap form POST Laravel agar terhindar dari serangan CSRF --}}
                @csrf

                {{-- Kolom input Nama Layanan --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Layanan</label>
                    <input type="text" name="nama_layanan" placeholder="Contoh: Cuci Sepatu" required
                           class="w-full border-gray-300 rounded-md shadow-sm text-sm p-2 border">
                </div>

                {{-- Kolom pilihan Satuan (kg atau pcs) --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Satuan Hitung</label>
                    <select name="satuan" required class="w-full border-gray-300 rounded-md shadow-sm text-sm p-2 border">
                        <option value="kg">Per Kilogram (kg)</option>
                        <option value="pcs">Per Potong (pcs)</option>
                    </select>
                </div>

                {{-- Kolom input Harga Dasar (untuk layanan Reguler) --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Harga Dasar Reguler (Rp)</label>
                    <input type="number" name="harga_dasar" placeholder="Contoh: 7000" min="1000" required
                           class="w-full border-gray-300 rounded-md shadow-sm text-sm p-2 border">
                </div>

                {{-- Kolom input Deskripsi & Tombol Simpan --}}
                <div class="flex flex-col justify-end">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Deskripsi Singkat</label>
                    <div class="flex gap-2">
                        <input type="text" name="deskripsi" placeholder="Keterangan layanan..."
                               class="w-full border-gray-300 rounded-md shadow-sm text-sm p-2 border">
                        {{-- Tombol submit untuk menyimpan layanan baru --}}
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-md text-sm whitespace-nowrap">
                            + Tambah
                        </button>
                    </div>
                </div>
            </form>

            {{-- Tabel Daftar Layanan Laundry yang sudah tersimpan di database --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    {{-- Kepala tabel daftar layanan --}}
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 border-b">
                            <th class="p-3">Nama Layanan</th>
                            <th class="p-3">Satuan</th>
                            <th class="p-3">Harga Dasar (Reguler)</th>
                            <th class="p-3">Deskripsi</th>
                            <th class="p-3 text-center">Aksi (Update / Hapus)</th>
                        </tr>
                    </thead>
                    {{-- Isi baris tabel daftar layanan --}}
                    <tbody class="divide-y">
                        {{-- Melakukan perulangan (looping) untuk setiap data layanan di variabel $services --}}
                        @forelse($services as $service)
                            <tr class="hover:bg-gray-50">
                                {{-- Form inline agar Admin bisa langsung mengubah data di baris tabel lalu klik Simpan --}}
                                <form action="{{ route('admin.services.update', $service->id) }}" method="POST">
                                    {{-- Token keamanan CSRF --}}
                                    @csrf
                                    {{-- Mengubah metode form menjadi PUT karena proses update data --}}
                                    @method('PUT')

                                    {{-- Kolom edit Nama Layanan --}}
                                    <td class="p-2">
                                        <input type="text" name="nama_layanan" value="{{ $service->nama_layanan }}" required
                                               class="w-full border rounded px-2 py-1 text-sm">
                                    </td>

                                    {{-- Kolom edit Satuan (kg / pcs) --}}
                                    <td class="p-2">
                                        <select name="satuan" class="border rounded px-2 py-1 text-sm">
                                            {{-- Jika satuan layanan 'kg', otomatis terpilih (selected) --}}
                                            <option value="kg" {{ $service->satuan === 'kg' ? 'selected' : '' }}>kg</option>
                                            {{-- Jika satuan layanan 'pcs', otomatis terpilih (selected) --}}
                                            <option value="pcs" {{ $service->satuan === 'pcs' ? 'selected' : '' }}>pcs</option>
                                        </select>
                                    </td>

                                    {{-- Kolom edit Harga Dasar --}}
                                    <td class="p-2">
                                        <input type="number" name="harga_dasar" value="{{ $service->harga_dasar }}" min="1000" required
                                               class="w-32 border rounded px-2 py-1 text-sm">
                                    </td>

                                    {{-- Kolom edit Deskripsi --}}
                                    <td class="p-2">
                                        <input type="text" name="deskripsi" value="{{ $service->deskripsi }}"
                                               class="w-full border rounded px-2 py-1 text-sm">
                                    </td>

                                    {{-- Kolom tombol Simpan Perubahan dan tombol Hapus --}}
                                    <td class="p-2 text-center flex items-center justify-center gap-2">
                                        {{-- Tombol simpan hasil edit baris layanan ini --}}
                                        <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1 rounded text-xs font-semibold">
                                            Simpan
                                        </button>
                                </form>

                                        {{-- Form terpisah khusus untuk tombol Hapus Layanan --}}
                                        <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus layanan ini?');">
                                            {{-- Token keamanan CSRF --}}
                                            @csrf
                                            {{-- Mengubah metode form menjadi DELETE untuk menghapus data --}}
                                            @method('DELETE')
                                            {{-- Tombol hapus layanan --}}
                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-semibold">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                            </tr>
                        {{-- Jika tabel services masih kosong, tampilkan pesan berikut --}}
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-gray-500">Belum ada layanan laundry.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ============================================================== --}}
        {{-- BAGIAN 3: TABEL REKAP PENJUALAN, FILTER, & CETAK STRUK         --}}
        {{-- ============================================================== --}}
        <div class="bg-white p-6 rounded-xl shadow space-y-4">
            {{-- Judul seksi Rekap Penjualan --}}
            <h3 class="text-lg font-bold text-gray-800 border-b pb-2">2. Rekap Penjualan & Transaksi Pelanggan</h3>

            {{-- Form Filter Rekap Penjualan menggunakan metode GET agar parameter muncul di URL --}}
            <form action="{{ route('admin.dashboard') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3 bg-gray-50 p-3 rounded-lg border">
                {{-- Dropdown Filter berdasarkan Status Cucian (tetap menjaga pilihan terakhir dengan request('status')) --}}
                <select name="status" class="border rounded px-3 py-1.5 text-sm">
                    <option value="">-- Semua Status --</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="diproses" {{ request('status') === 'diproses' ? 'selected' : '' }}>Diproses</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>

                {{-- Dropdown Filter berdasarkan Tipe Kecepatan (tetap menjaga pilihan terakhir dengan request('tipe_kecepatan')) --}}
                <select name="tipe_kecepatan" class="border rounded px-3 py-1.5 text-sm">
                    <option value="">-- Semua Kecepatan --</option>
                    <option value="reguler" {{ request('tipe_kecepatan') === 'reguler' ? 'selected' : '' }}>Reguler</option>
                    <option value="cepat" {{ request('tipe_kecepatan') === 'cepat' ? 'selected' : '' }}>Cepat</option>
                    <option value="lebih_cepat" {{ request('tipe_kecepatan') === 'lebih_cepat' ? 'selected' : '' }}>Lebih Cepat</option>
                </select>

                {{-- Input Kalender untuk menyaring transaksi berdasarkan Tanggal tertentu --}}
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="border rounded px-3 py-1.5 text-sm">

                {{-- Tombol Terapkan Filter dan Tombol Reset (menghapus semua filter di URL) --}}
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-sm font-semibold">
                        Filter
                    </button>
                    {{-- Link Reset mengembalikan ke rute admin.dashboard tanpa parameter filter --}}
                    <a href="{{ route('admin.dashboard') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-3 py-1.5 rounded text-sm font-semibold text-center">
                        Reset
                    </a>
                </div>
            </form>

            {{-- Pembungkus tabel agar responsif (bisa digeser ke samping di layar kecil) --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    {{-- Baris judul kolom tabel rekap penjualan --}}
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 border-b">
                            <th class="p-3">Tanggal</th>
                            <th class="p-3">Nama Pelanggan</th>
                            <th class="p-3">Layanan</th>
                            <th class="p-3">Kecepatan</th>
                            <th class="p-3">Jumlah</th>
                            <th class="p-3">Total Harga</th>
                            <th class="p-3">Catatan / Alamat</th>
                            <th class="p-3">Status Cucian</th>
                            {{-- Kolom baru khusus tombol cetak struk --}}
                            <th class="p-3">Struk</th>
                        </tr>
                    </thead>
                    {{-- Isi data transaksi --}}
                    <tbody class="divide-y">
                        {{-- Menampilkan setiap transaksi dari variabel $transactions --}}
                        @forelse($transactions as $trx)
                            <tr class="hover:bg-gray-50">
                                {{-- Menampilkan tanggal dan jam transaksi dibuat --}}
                                <td class="p-3 text-gray-600">{{ $trx->created_at->format('d M Y, H:i') }}</td>

                                {{-- Menampilkan nama pelanggan dari relasi tabel users --}}
                                <td class="p-3 font-semibold text-gray-800">{{ $trx->user->name ?? '-' }}</td>

                                {{-- Menampilkan nama layanan dari relasi tabel services --}}
                                <td class="p-3">{{ $trx->service->nama_layanan ?? '-' }}</td>

                                {{-- Menampilkan badge warna tipe kecepatan (reguler, cepat, atau lebih_cepat) --}}
                                <td class="p-3">
                                    @if($trx->tipe_kecepatan === 'reguler')
                                        <span class="bg-gray-200 text-gray-800 px-2 py-1 rounded text-xs font-semibold">Reguler (2-3 Hari)</span>
                                    @elseif($trx->tipe_kecepatan === 'cepat')
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-semibold">Cepat (1 Hari)</span>
                                    @else
                                        <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs font-semibold">Lebih Cepat (6 Jam)</span>
                                    @endif
                                </td>

                                {{-- Menampilkan berat/jumlah beserta satuannya (kg/pcs) --}}
                                <td class="p-3">{{ $trx->jumlah }} {{ $trx->service->satuan ?? 'kg' }}</td>

                                {{-- Menampilkan total harga transaksi dalam format Rupiah --}}
                                <td class="p-3 font-bold text-emerald-700">
                                    Rp {{ number_format($trx->total_harga, 0, ',', '.') }}
                                </td>

                                {{-- Menampilkan catatan atau alamat pelanggan (jika kosong tampilkan tanda -) --}}
                                <td class="p-3 text-gray-600">{{ $trx->catatan ?: '-' }}</td>

                                {{-- Kolom untuk mengubah status pesanan (pending -> diproses -> selesai) --}}
                                <td class="p-3">
                                    <form action="{{ route('admin.transactions.status', $trx->id) }}" method="POST" class="flex items-center gap-2">
                                        {{-- Token keamanan CSRF --}}
                                        @csrf
                                        {{-- Menggunakan method PATCH untuk update sebagian data (status) --}}
                                        @method('PATCH')
                                        {{-- Dropdown pilihan status cucian --}}
                                        <select name="status" class="border rounded px-2 py-1 text-xs font-semibold">
                                            <option value="pending" {{ $trx->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="diproses" {{ $trx->status === 'diproses' ? 'selected' : '' }}>Diproses</option>
                                            <option value="selesai" {{ $trx->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                        </select>
                                        {{-- Tombol simpan perubahan status --}}
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-2.5 py-1 rounded text-xs">
                                            Update
                                        </button>
                                    </form>
                                </td>

                                {{-- Kolom baru: Tombol membuka halaman cetak struk transaksi di tab baru (target="_blank") --}}
                                <td class="p-3">
                                    <a href="{{ route('struk.cetak', $trx->id) }}" target="_blank"
                                       class="bg-emerald-600 hover:bg-emerald-700 text-white px-2.5 py-1 rounded text-xs font-semibold inline-block">
                                        Struk
                                    </a>
                                </td>
                            </tr>
                        {{-- Jika tidak ada transaksi yang cocok dengan filter atau masih kosong --}}
                        @empty
                            <tr>
                                {{-- Menggunakan colspan="9" karena jumlah kolom tabel sekarang ada 9 --}}
                                <td colspan="9" class="p-4 text-center text-gray-500">Belum ada transaksi penjualan yang sesuai filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>