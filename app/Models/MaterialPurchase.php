<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialPurchase extends Model
{
    use HasFactory;

    protected $table = 'material_purchases';
    public $timestamps = false;

    protected $fillable = [
        'MaterialID',
        'Jumlah',
        'HargaSatuan',
        'Total',
        'Supplier',
        'Tanggal',
        'CreatedBy',
        'Catatan',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class, 'MaterialID', 'MaterialID');
    }
}
