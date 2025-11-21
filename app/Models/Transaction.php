<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';
    protected $primaryKey = 'TransaksiID';
    public $timestamps = false;

    protected $fillable = [
        'OrderID',
        'JenisTransaksi',
        'Jumlah',
        'Tanggal',
        'Keterangan',
    ];

    protected $casts = [
        'Tanggal' => 'date',
        'Jumlah' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'OrderID');
    }
}