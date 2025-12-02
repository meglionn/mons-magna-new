<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $primaryKey = 'OrderID';
    public $timestamps = false;

    protected $fillable = [
        'CustomerID',
        'Tanggal',
        'StatusOrder',
        'TotalHarga',
    ];

    protected $casts = [
        'Tanggal' => 'date',
        'TotalHarga' => 'decimal:2',
    ];

    /**
     * Relasi ke Customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'CustomerID', 'CustomerID');
    }

    /**
     * Relasi ke OrderDetails (one-to-many)
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'OrderID', 'OrderID');
    }

    /**
     * Relasi ke Produksi (one-to-many)
     */
    public function produksi()
    {
        return $this->hasMany(Produksi::class, 'OrderID', 'OrderID');
    }

    /**
     * Relasi ke CustomDetail (one-to-one)
     */
    public function customDetail()
    {
        return $this->hasOne(CustomDetail::class, 'OrderID', 'OrderID');
    }

    /**
     * Relasi ke Transactions
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'OrderID', 'OrderID');
    }

    /**
     * Check if order is custom order
     */
    public function isCustomOrder()
    {
        return $this->customDetail()->exists();
    }

    /**
     * Check if order is production order
     */
    public function isProductionOrder()
    {
        return $this->orderDetails()->exists();
    }

    /**
     * Calculate total from order details
     */
    public function calculateTotal()
    {
        return $this->orderDetails()->sum('Subtotal');
    }

    /**
     * Auto-calculate total when saving
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($order) {
            if (!$order->TotalHarga && $order->orderDetails()->exists()) {
                $order->TotalHarga = $order->calculateTotal();
                $order->saveQuietly(); // Save without triggering events
            }
        });
    }
}