<?php

use Illuminate\Foundation\Application; // Mengimpor class inti aplikasi Laravel
use Illuminate\Foundation\Configuration\Exceptions; // Mengimpor pengaturan penanganan error
use Illuminate\Foundation\Configuration\Middleware; // Mengimpor pengaturan middleware Laravel 11
use App\Http\Middleware\RoleMiddleware; // Mengimpor RoleMiddleware yang baru saja kita buat

// Mengonfigurasi dan menjalankan kerangka utama aplikasi Laravel
return Application::configure(basePath: dirname(__DIR__))
    // Menentukan lokasi file-file rute (web, console, dan health check)
    ->withRouting(
        web: __DIR__.'/../routes/web.php', // Lokasi rute halaman web utama
        commands: __DIR__.'/../routes/console.php', // Lokasi rute perintah terminal (artisan)
        health: '/up', // URL untuk mengecek apakah server menyala
    )
    // Bagian untuk mendaftarkan middleware tambahan
    ->withMiddleware(function (Middleware $middleware) {
        // Mendaftarkan nama panggilan (alias) 'role' yang terhubung ke class RoleMiddleware
        $middleware->alias([
            'role' => RoleMiddleware::class, // Sekarang kita bisa memakai 'role:admin' atau 'role:user' di web.php
        ]);
    })
    // Bagian pengaturan error bawaan Laravel
    ->withExceptions(function (Exceptions $exceptions) {
        // Dibiarkan kosong sesuai standar bawaan Laravel
    })->create(); // Menyelesaikan pembuatan konfigurasi aplikasi