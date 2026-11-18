<?php

use App\Http\Controllers\ProfileController; // Mengimpor ProfileController bawaan Laravel Breeze
use App\Http\Controllers\AdminController; // Mengimpor AdminController yang kita buat
use App\Http\Controllers\UserController; // Mengimpor UserController yang kita buat
use Illuminate\Support\Facades\Route; // Mengimpor fitur Route untuk mendaftarkan alamat URL
use Illuminate\Support\Facades\Auth; // Mengimpor fitur Auth untuk mengecek role user saat login

// Rute halaman paling depan (saat membuka localhost:8000) langsung diarahkan ke halaman login
Route::get('/', function () {
    return redirect()->route('login'); // Mengalihkan pengunjung langsung ke form login
});

// Rute '/dashboard' bawaan Breeze yang otomatis memeriksa role setelah user berhasil login
Route::get('/dashboard', function () {
    // Jika user yang login memiliki role 'admin', arahkan ke dashboard Admin
    if (Auth::user()->role === 'admin') {
        return redirect()->route('admin.dashboard'); // Pindah ke URL /admin/dashboard
    }
    // Jika bukan admin (berarti role 'user'), arahkan ke dashboard User
    return redirect()->route('user.dashboard'); // Pindah ke URL /user/dashboard
})->middleware(['auth', 'verified'])->name('dashboard'); // Wajib sudah login

// ==========================================
// GRUP RUTE KHUSUS ADMIN (Role: admin)
// ==========================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Rute untuk menampilkan halaman utama Admin (Kelola Layanan & Rekap Penjualan)
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // Rute untuk menyimpan layanan laundry baru (Method POST)
    Route::post('/services', [AdminController::class, 'storeService'])->name('services.store');

    // Rute untuk mengubah/edit layanan laundry berdasarkan ID (Method PUT)
    Route::put('/services/{id}', [AdminController::class, 'updateService'])->name('services.update');

    // Rute untuk menghapus layanan laundry berdasarkan ID (Method DELETE)
    Route::delete('/services/{id}', [AdminController::class, 'destroyService'])->name('services.destroy');

    // Rute untuk mengubah status transaksi cucian di tabel Rekap Penjualan (Method PATCH)
    Route::patch('/transactions/{id}/status', [AdminController::class, 'updateStatus'])->name('transactions.status');
});

// ==========================================
// GRUP RUTE KHUSUS PELANGGAN (Role: user)
// ==========================================
Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    // Rute untuk menampilkan halaman opsi layanan laundry bagi pelanggan
    Route::get('/dashboard', [UserController::class, 'index'])->name('dashboard');

    // Rute untuk mengirim form pemesanan laundry baru (Method POST)
    Route::post('/order', [UserController::class, 'storeOrder'])->name('order.store');
});

// Rute pengaturan profil bawaan Laravel Breeze
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit'); // Halaman edit profil
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update'); // Simpan profil
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy'); // Hapus akun
});

// Memuat seluruh rute autentikasi (login, register, logout) dari file routes/auth.php
require __DIR__.'/auth.php';