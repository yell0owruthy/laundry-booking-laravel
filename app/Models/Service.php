<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_layanan',
        'satuan',
        'harga_dasar',
        'deskripsi',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}