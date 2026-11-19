<?php

namespace App\Http\Controllers; // Menentukan lokasi namespace controller

use App\Models\Service; // Mengimpor Model Service untuk mengakses tabel services
use App\Models\Transaction; // Mengimpor Model Transaction untuk mengakses tabel transactions
use Illuminate\Http\Request; // Mengimpor class Request untuk menangkap input dari form HTML

class AdminController extends Controller
{
    // 1. Fungsi untuk menampilkan halaman utama Admin (Kelola Layanan & Rekap Penjualan)
    // 1. Fungsi menampilkan halaman Admin beserta fitur Filter Rekap Penjualan
   // 1. Fungsi untuk menampilkan halaman utama Admin (Kelola Layanan & Rekap Penjualan)
    public function index()
    {
        // Mengambil semua data layanan laundry dari yang terbaru
        $services = Service::latest()->get();

        // Mengambil semua data transaksi beserta data user dan layanan terkait
        $transactions = Transaction::with(['user', 'service'])->latest()->get();

        // Menghitung total seluruh uang penjualan dari kolom total_harga
        $totalPendapatan = Transaction::sum('total_harga');

        // Menghitung total jumlah transaksi yang pernah masuk
        $totalTransaksi = Transaction::count();

        // Mengirimkan data ke tampilan resources/views/admin/dashboard.blade.php
        return view('admin.dashboard', compact('services', 'transactions', 'totalPendapatan', 'totalTransaksi'));
    }
    // 2. Fungsi untuk menyimpan layanan laundry baru yang diinput oleh Admin
    public function storeService(Request $request)
    {
        // Memvalidasi input dari form agar tidak ada data yang kosong atau salah tipe
        $request->validate([
            'nama_layanan' => 'required|string|max:255', // Nama layanan wajib diisi berupa teks
            'satuan' => 'required|in:kg,pcs', // Satuan wajib dipilih antara 'kg' atau 'pcs'
            'harga_dasar' => 'required|integer|min:1000', // Harga dasar wajib angka minimal Rp1.000
            'deskripsi' => 'nullable|string', // Deskripsi boleh dikosongkan
        ]);

        // Menyimpan data layanan baru ke dalam tabel services di database
        Service::create([
            'nama_layanan' => $request->nama_layanan, // Mengisi kolom nama_layanan dari input form
            'satuan' => $request->satuan, // Mengisi kolom satuan dari input form
            'harga_dasar' => $request->harga_dasar, // Mengisi kolom harga_dasar dari input form
            'deskripsi' => $request->deskripsi, // Mengisi kolom deskripsi dari input form
        ]);

        // Mengembalikan Admin ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', 'Layanan laundry berhasil ditambahkan!');
    }

    // 3. Fungsi untuk memperbarui (edit) data layanan laundry yang sudah ada
    public function updateService(Request $request, $id)
    {
        // Memvalidasi data ubahan yang dikirim dari form edit layanan
        $request->validate([
            'nama_layanan' => 'required|string|max:255', // Nama layanan wajib diisi
            'satuan' => 'required|in:kg,pcs', // Satuan wajib 'kg' atau 'pcs'
            'harga_dasar' => 'required|integer|min:1000', // Harga dasar wajib angka minimal 1000
            'deskripsi' => 'nullable|string', // Deskripsi opsional
        ]);

        // Mencari data layanan berdasarkan ID, jika tidak ketemu otomatis tampil 404
        $service = Service::findOrFail($id);

        // Mengubah data layanan di database dengan nilai baru dari form
        $service->update([
            'nama_layanan' => $request->nama_layanan, // Update nama layanan
            'satuan' => $request->satuan, // Update satuan (kg/pcs)
            'harga_dasar' => $request->harga_dasar, // Update harga dasar
            'deskripsi' => $request->deskripsi, // Update deskripsi
        ]);

        // Kembali ke halaman dashboard Admin dengan notifikasi berhasil diubah
        return redirect()->back()->with('success', 'Data layanan berhasil diperbarui!');
    }

    // 4. Fungsi untuk menghapus layanan laundry dari database
    public function destroyService($id)
    {
        // Mencari layanan berdasarkan ID yang ingin dihapus
        $service = Service::findOrFail($id);

        // Menghapus baris layanan tersebut dari tabel services
        $service->delete();

        // Kembali ke halaman Admin dengan pesan bahwa layanan telah dihapus
        return redirect()->back()->with('success', 'Layanan laundry berhasil dihapus!');
    }

    // 5. Fungsi untuk mengubah status cucian pelanggan (pending / diproses / selesai)
    public function updateStatus(Request $request, $id)
    {
        // Memastikan status yang dipilih hanya salah satu dari pending, diproses, atau selesai
        $request->validate([
            'status' => 'required|in:pending,diproses,selesai', // Validasi pilihan status
        ]);

        // Mencari data transaksi berdasarkan ID transaksi
        $transaction = Transaction::findOrFail($id);

        // Menyimpan perubahan status cucian ke database
        $transaction->update([
            'status' => $request->status, // Mengganti status lama dengan status baru
        ]);

        // Kembali ke halaman Admin dengan pesan sukses update status
        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui!');
    }
}