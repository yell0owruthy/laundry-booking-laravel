<?php

namespace App\Http\Controllers; // Menentukan lokasi namespace controller

use App\Models\Service; // Mengimpor Model Service untuk menampilkan daftar layanan ke pelanggan
use App\Models\Transaction; // Mengimpor Model Transaction untuk menyimpan pesanan pelanggan
use Illuminate\Http\Request; // Mengimpor class Request untuk membaca input pesanan dari form
use Illuminate\Support\Facades\Auth; // Mengimpor Auth untuk mengambil ID user yang sedang login

class UserController extends Controller
{
    // 1. Fungsi untuk menampilkan halaman User (Opsi Layanan & Riwayat Pesanan Saya)
   // 1. Fungsi menampilkan halaman User + Filter Kategori Kecepatan pada Riwayat Pesanan Saya
    public function index(Request $request)
    {
        // Mengambil semua daftar layanan laundry yang tersedia
        $services = Service::all();

        // Menyiapkan query riwayat transaksi khusus milik user yang sedang login
        $query = Transaction::with('service') // Mengambil relasi nama layanan
            ->where('user_id', Auth::id()) // Memfilter hanya transaksi milik user yang login
            ->latest(); // Mengurutkan dari pesanan paling baru

        // Mengecek apakah user memilih filter kecepatan (reguler / cepat / lebih_cepat)
        if ($request->filled('kecepatan')) {
            // Menyaring riwayat pesanan berdasarkan kolom tipe_kecepatan yang dipilih
            $query->where('tipe_kecepatan', $request->kecepatan);
        }

        // Mengeksekusi query menjadi kumpulan data riwayat pesanan
        $myTransactions = $query->get();

        // Mengirimkan data layanan dan riwayat pesanan ke resources/views/user/dashboard.blade.php
        return view('user.dashboard', compact('services', 'myTransactions'));
    }

    // 2. Fungsi untuk memproses pemesanan laundry baru dari User
    public function storeOrder(Request $request)
    {
        // Memvalidasi input form pemesanan agar data yang dikirim lengkap dan valid
        $request->validate([
            'service_id' => 'required|exists:services,id', // Layanan yang dipilih harus ada di tabel services
            'tipe_kecepatan' => 'required|in:reguler,cepat,lebih_cepat', // Pilihan kecepatan wajib valid
            'jumlah' => 'required|integer|min:1', // Berat (kg) atau jumlah potong minimal 1
            'catatan' => 'nullable|string|max:500', // Catatan atau alamat bersifat opsional
        ]);

        // Mengambil detail layanan yang dipilih user dari database untuk mengecek harga dasarnya
        $service = Service::findOrFail($request->service_id);

        // Menyiapkan variabel biaya tambahan per kg/pcs dengan nilai awal 0 (untuk Reguler)
        $biayaTambahan = 0;

        // Jika user memilih tipe kecepatan 'cepat' (estimasi 1 hari), tambah biaya Rp5.000 per satuan
        if ($request->tipe_kecepatan === 'cepat') {
            $biayaTambahan = 5000; // Biaya tambahan layanan Cepat
        }
        // Jika user memilih tipe kecepatan 'lebih_cepat' (estimasi 6 jam), tambah biaya Rp10.000 per satuan
        elseif ($request->tipe_kecepatan === 'lebih_cepat') {
            $biayaTambahan = 10000; // Biaya tambahan layanan Lebih Cepat (Express)
        }

        // Menghitung harga per satuan setelah ditambah biaya kecepatan
        $hargaPerSatuanFinal = $service->harga_dasar + $biayaTambahan;

        // Menghitung total harga keseluruhan (Harga per satuan final dikali jumlah kg/pcs)
        $totalHarga = $hargaPerSatuanFinal * $request->jumlah;

        // Menyimpan data transaksi pesanan ke dalam tabel transactions
        Transaction::create([
            'user_id' => Auth::id(), // Menyimpan ID pelanggan yang sedang login
            'service_id' => $service->id, // Menyimpan ID layanan yang dipilih
            'tipe_kecepatan' => $request->tipe_kecepatan, // Menyimpan opsi reguler / cepat / lebih_cepat
            'jumlah' => $request->jumlah, // Menyimpan berat cucian (kg) atau jumlah potong (pcs)
            'biaya_tambahan_per_satuan' => $biayaTambahan, // Menyimpan nominal tambahan (0 / 5000 / 10000)
            'total_harga' => $totalHarga, // Menyimpan hasil perhitungan total harga
            'catatan' => $request->catatan, // Menyimpan catatan atau alamat pelanggan
            'status' => 'pending', // Status awal pesanan otomatis diatur 'pending'
        ]);

        // Mengembalikan user ke halaman dashboard dengan pesan bahwa pesanan berhasil dibuat
        return redirect()->back()->with('success', 'Pesanan laundry berhasil dibuat! Total biaya: Rp ' . number_format($totalHarga, 0, ',', '.'));
    }

    // 3. Fungsi untuk menampilkan halaman cetak struk transaksi laundry
    public function cetakStruk($id)
    {
        // Mencari data transaksi berdasarkan ID lengkap dengan nama pelanggan dan layanan
        $transaction = Transaction::with(['user', 'service'])->findOrFail($id);

        // Keamanan: Jika yang login bukan admin DAN bukan pemilik transaksi, tolak aksesnya
        if (Auth::user()->role !== 'admin' && $transaction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki izin untuk mencetak struk pesanan orang lain.');
        }

        // Menampilkan halaman struk di resources/views/struk.blade.php
        return view('struk', compact('transaction'));
    }
}