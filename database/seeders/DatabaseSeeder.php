<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;
use App\Models\Transaction;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed Materials
        Material::create([
            'NamaBahan' => 'Kulit Sapi Premium',
            'Kategori' => 'Kulit',
            'StokBahan' => 15,
            'MinimumStok' => 10,
            'HargaSatuan' => 500000,
            'JenisBahan' => 'Full Grain Leather'
        ]);

        Material::create([
            'NamaBahan' => 'Benang Nilon',
            'Kategori' => 'Benang',
            'StokBahan' => 4,
            'MinimumStok' => 10,
            'HargaSatuan' => 30000,
            'JenisBahan' => 'Nylon Thread'
        ]);

        // Seed Customers
        Customer::create([
            'Nama' => 'Budi Santoso',
            'Email' => 'budi@example.com',
            'NoTelp' => '081234567890',
            'Alamat' => 'Jl. Merdeka No. 123, Jakarta'
        ]);

        Customer::create([
            'Nama' => 'Agus Wijaya',
            'Email' => 'agus@example.com',
            'NoTelp' => '081234567891',
            'Alamat' => 'Jl. Sudirman No. 456, Bandung'
        ]);

        // Seed Products
        Product::create([
            'NamaProduk' => 'Sepatu Kasual Pria',
            'JenisProduk' => 'Kasual',
            'Model' => 'Classic Oxford',
            'Ukuran' => 42,
            'Harga' => 750000
        ]);

        Product::create([
            'NamaProduk' => 'Sepatu Kulit Custom',
            'JenisProduk' => 'Custom',
            'Model' => 'Derby',
            'Ukuran' => 42,
            'Harga' => 1200000
        ]);

        // Seed Orders
        $order1 = Order::create([
            'CustomerID' => 1,
            'Tanggal' => now(),
            'StatusOrder' => 'Proses',
            'TotalHarga' => 15000000
        ]);

        $order2 = Order::create([
            'CustomerID' => 2,
            'Tanggal' => now()->subDays(5),
            'StatusOrder' => 'Tertunda',
            'TotalHarga' => 1200000
        ]);

        // Seed Transactions
        Transaction::create([
            'OrderID' => $order1->OrderID,
            'JenisTransaksi' => 'Pemasukan',
            'Jumlah' => 1200000,
            'Tanggal' => now(),
            'Keterangan' => 'Penjualan sepatu kulit'
        ]);

        Transaction::create([
            'OrderID' => null,
            'JenisTransaksi' => 'Pengeluaran',
            'Jumlah' => 850000,
            'Tanggal' => now()->subDays(1),
            'Keterangan' => 'Pembelian kulit sapi premium'
        ]);
    }
}