<?php

// app/Models/Transaksi.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = ['invoice_code', 'total_items', 'total_price','change','bayar'];

    public function details()
    {
        return $this->hasMany(DetailTransaksi::class);
    }
}