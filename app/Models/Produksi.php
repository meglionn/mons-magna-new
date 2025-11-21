<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produksi extends Model
{
    use HasFactory;

    protected $table = 'produksi';
    protected $primaryKey = 'ProduksiID';
    public $timestamps = false;

    protected $fillable = [
        'OrderID',
        'TanggalMulai',
        'TanggalSelesai',
        'StatusProduksi',
        'Keterangan',
    ];

    protected $casts = [
        'TanggalMulai' => 'date',
        'TanggalSelesai' => 'date',
    ];

    /**
     * Relasi ke Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'OrderID', 'OrderID');
    }

    /**
     * Check if production is completed
     */
    public function isCompleted()
    {
        return $this->StatusProduksi === 'Selesai';
    }

    /**
     * Check if production is in progress
     */
    public function isInProgress()
    {
        return $this->StatusProduksi === 'Dalam Proses';
    }
}