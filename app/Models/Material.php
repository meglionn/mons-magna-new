<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $table = 'materials';
    protected $primaryKey = 'MaterialID';
    public $timestamps = false;

    protected $fillable = [
        'NamaBahan',
        'Kategori',
        'StokBahan',
        'MinimumStok',
        'HargaSatuan',
        'JenisBahan',
    ];

    protected $casts = [
        'HargaSatuan' => 'decimal:2',
    ];
}