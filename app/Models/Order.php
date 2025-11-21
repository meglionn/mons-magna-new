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

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'CustomerID');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'OrderID');
    }

    public function customDetail()
    {
        return $this->hasOne(CustomDetail::class, 'OrderID');
    }
}