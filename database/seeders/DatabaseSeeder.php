<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\CustomDetail;
use App\Models\Transaction;
use App\Models\Produksi;

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
        $customer1 = Customer::create([
            'Nama' => 'Budi Santoso',
            'Email' => 'budi@example.com',
            'NoTelp' => '081234567890',
            'Alamat' => 'Jl. Merdeka No. 123, Jakarta'
        ]);

        $customer2 = Customer::create([
            'Nama' => 'Agus Wijaya',
            'Email' => 'agus@example.com',
            'NoTelp' => '081234567891',
            'Alamat' => 'Jl. Sudirman No. 456, Bandung'
        ]);

        // Seed Products
        $product1 = Product::create([
            'NamaProduk' => 'Sepatu Kasual Pria',
            'JenisProduk' => 'Kasual',
            'Model' => 'Classic Oxford',
            'Ukuran' => 42,
            'Harga' => 750000
        ]);

        $product2 = Product::create([
            'NamaProduk' => 'Sepatu Kulit Custom',
            'JenisProduk' => 'Custom',
            'Model' => 'Derby',
            'Ukuran' => 42,
            'Harga' => 1200000
        ]);

        // New signature products
        Product::create([
            'NamaProduk' => 'Kalla Baiq',
            'JenisProduk' => 'Signature',
            'Model' => 'Oxford',
            'Ukuran' => 42,
            'Harga' => 1350000
        ]);

        Product::create([
            'NamaProduk' => 'Lana Lale',
            'JenisProduk' => 'Signature',
            'Model' => 'Loafer',
            'Ukuran' => 42,
            'Harga' => 1250000
        ]);

        Product::create([
            'NamaProduk' => 'Orlin Sesak',
            'JenisProduk' => 'Signature',
            'Model' => 'Derby',
            'Ukuran' => 42,
            'Harga' => 1400000
        ]);

        Product::create([
            'NamaProduk' => 'Remo Lalu',
            'JenisProduk' => 'Signature',
            'Model' => 'Monk Strap',
            'Ukuran' => 42,
            'Harga' => 1280000
        ]);

        // Seed Production Order
        $order1 = Order::create([
            'CustomerID' => $customer1->CustomerID,
            'Tanggal' => now(),
            'StatusOrder' => 'Proses',
            'TotalHarga' => 1500000
        ]);

        // Seed Order Details (Production Order)
        OrderDetail::create([
            'OrderID' => $order1->OrderID,
            'ProductID' => $product1->ProductID,
            'Jumlah' => 2,
            'HargaSatuan' => 750000,
            'Subtotal' => 1500000
        ]);

        // Seed Production
        Produksi::create([
            'OrderID' => $order1->OrderID,
            'TanggalMulai' => now(),
            'TanggalSelesai' => null,
            'StatusProduksi' => 'Dalam Proses',
            'Keterangan' => 'Produksi sepatu kasual untuk Budi Santoso'
        ]);

        // Seed Custom Order
        $order2 = Order::create([
            'CustomerID' => $customer2->CustomerID,
            'Tanggal' => now()->subDays(5),
            'StatusOrder' => 'Tertunda',
            'TotalHarga' => 1200000
        ]);

        // Seed Custom Details
        CustomDetail::create([
            'OrderID' => $order2->OrderID,
            'JenisBahan' => 'Full Grain Leather',
            'Warna' => 'Hitam',
            'Ukuran' => 42,
            'Model' => 'Derby',
            'CatatanTambahan' => 'Tambahkan inisial "AW" di bagian dalam'
        ]);

        // Seed Transactions
        Transaction::create([
            'OrderID' => $order1->OrderID,
            'JenisTransaksi' => 'Pemasukan',
            'Jumlah' => 1500000,
            'Tanggal' => now(),
            'Keterangan' => 'Penjualan sepatu kasual'
        ]);

        Transaction::create([
            'OrderID' => null,
            'JenisTransaksi' => 'Pengeluaran',
            'Jumlah' => 850000,
            'Tanggal' => now()->subDays(1),
            'Keterangan' => 'Pembelian kulit sapi premium'
        ]);

        // Add more customers for testing
        Customer::create([
            'Nama' => 'Siti Rahma',
            'Email' => 'siti@example.com',
            'NoTelp' => '081234567892',
            'Alamat' => 'Jl. Ahmad Yani No. 789, Surabaya'
        ]);

        Customer::create([
            'Nama' => 'Dedi Kurniawan',
            'Email' => 'dedi@example.com',
            'NoTelp' => '081234567893',
            'Alamat' => 'Jl. Gatot Subroto No. 101, Semarang'
        ]);
    }

}