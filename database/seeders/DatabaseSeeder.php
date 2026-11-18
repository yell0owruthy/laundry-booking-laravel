<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Akun Admin
        User::create([
            'name' => 'Admin Laundry',
            'email' => 'admin@laundry.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 2. Buat Akun User Pelanggan
        User::create([
            'name' => 'Pelanggan Satu',
            'email' => 'user@laundry.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // 3. Buat Data Awal Layanan Laundry
        $services = [
            [
                'nama_layanan' => 'Cuci Komplit (Cuci + Kering + Setrika)',
                'satuan' => 'kg',
                'harga_dasar' => 7000,
                'deskripsi' => 'Pakaian dicuci bersih, dikeringkan, disetrika rapi, dan diberi parfum.',
            ],
            [
                'nama_layanan' => 'Cuci Kering Lipat',
                'satuan' => 'kg',
                'harga_dasar' => 5000,
                'deskripsi' => 'Pakaian dicuci bersih, dikeringkan, dan dilipat rapi tanpa setrika.',
            ],
            [
                'nama_layanan' => 'Setrika Uap Rapi',
                'satuan' => 'kg',
                'harga_dasar' => 4500,
                'deskripsi' => 'Layanan khusus setrika uap plus pelicin dan pewangi pakaian.',
            ],
            [
                'nama_layanan' => 'Cuci Bed Cover / Selimut Tebal',
                'satuan' => 'pcs',
                'harga_dasar' => 25000,
                'deskripsi' => 'Pencucian khusus bed cover atau selimut ukuran besar per potong.',
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}