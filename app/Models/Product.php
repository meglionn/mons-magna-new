<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'ProductID';
    public $timestamps = false;

    protected $fillable = [
        'NamaProduk',
        'JenisProduk',
        'Model',
        'Ukuran',
        'Harga',
    ];

    protected $casts = [
        'Harga' => 'decimal:2',
    ];
}