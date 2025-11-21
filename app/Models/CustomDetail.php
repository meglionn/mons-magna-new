<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomDetail extends Model
{
    use HasFactory;

    protected $table = 'customdetails';
    protected $primaryKey = 'CustomID';
    public $timestamps = false;

    protected $fillable = [
        'OrderID',
        'JenisBahan',
        'Warna',
        'Ukuran',
        'Model',
        'CatatanTambahan',
    ];

    /**
     * Relasi ke Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'OrderID', 'OrderID');
    }
}