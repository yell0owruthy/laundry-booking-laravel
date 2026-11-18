<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->enum('tipe_kecepatan', ['reguler', 'cepat', 'lebih_cepat'])->default('reguler');
            $table->integer('jumlah'); // Berat (kg) atau jumlah potong (pcs)
            $table->integer('biaya_tambahan_per_satuan')->default(0); // 0, 5000, atau 10000
            $table->integer('total_harga');
            $table->text('catatan')->nullable();
            $table->enum('status', ['pending', 'diproses', 'selesai'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};