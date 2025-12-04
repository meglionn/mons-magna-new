<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $table = 'orderdetails';
    protected $primaryKey = 'OrderDetailID';
    public $timestamps = false;

    protected $fillable = [
        'OrderID',
        'ProductID',
        'Jumlah',
        'HargaSatuan',
        'Subtotal',
        'Ukuran',
        'Warna',
        'JenisBahan',
    ];

    protected $casts = [
        'HargaSatuan' => 'decimal:2',
        'Subtotal' => 'decimal:2',
    ];

    /**
     * Relasi ke Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'OrderID', 'OrderID');
    }

    /**
     * Relasi ke Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'ProductID', 'ProductID');
    }

    /**
     * Boot method untuk auto-calculate subtotal
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($orderDetail) {
            if ($orderDetail->Jumlah && $orderDetail->HargaSatuan) {
                $orderDetail->Subtotal = $orderDetail->Jumlah * $orderDetail->HargaSatuan;
            }
        });
    }
}