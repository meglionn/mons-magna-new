<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';
    protected $primaryKey = 'CustomerID';
    public $timestamps = false;

    protected $fillable = [
        'Nama',
        'Email',
        'NoTelp',
        'Alamat',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'CustomerID');
    }
}