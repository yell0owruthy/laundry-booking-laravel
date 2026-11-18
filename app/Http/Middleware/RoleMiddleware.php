<?php

namespace App\Http\Middleware; // Menentukan lokasi folder (namespace) dari file middleware ini

use Closure; // Mengimpor class Closure untuk meneruskan request ke proses berikutnya
use Illuminate\Http\Request; // Mengimpor class Request untuk membaca data permintaan HTTP dari browser
use Illuminate\Support\Facades\Auth; // Mengimpor fitur Auth untuk mengecek siapa user yang sedang login
use Symfony\Component\HttpFoundation\Response; // Mengimpor tipe kembalian Response standar Laravel

class RoleMiddleware
{
    // Fungsi utama yang otomatis dijalankan saat user mengakses halaman berpelindung role
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Mengecek apakah user belum login sama sekali
        if (!Auth::check()) {
            // Jika belum login, paksa pindah (redirect) ke halaman login
            return redirect()->route('login');
        }

        // Mengecek apakah role user yang sedang login TIDAK SAMA dengan role yang diizinkan halaman
        if (Auth::user()->role !== $role) {
            // Jika tidak sesuai, hentikan proses dan tampilkan pesan error 403 (Akses Ditolak)
            abort(403, 'Akses ditolak! Anda tidak memiliki izin untuk membuka halaman ini.');
        }

        // Jika role sudah sesuai, izinkan user melanjutkan masuk ke halaman yang dituju
        return $next($request);
    }
}