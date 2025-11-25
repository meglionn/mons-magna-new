<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan {{ ucfirst($type) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .header {
            text-align: right;
            margin-bottom: 20px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        Tanggal: {{ $date }}
    </div>
    
    <h1>Laporan {{ $type == 'inventory' ? 'Inventori Material' : ($type == 'sales' ? 'Penjualan' : 'Keuangan') }}</h1>
    
    <table>
        <thead>
            <tr>
                @if($type == 'inventory')
                    <th>Material</th>
                    <th>SKU</th>
                    <th>Stok</th>
                    <th>Satuan</th>
                    <th>Nilai (IDR)</th>
                    <th>Status</th>
                @elseif($type == 'sales')
                    <th>Produk</th>
                    <th>SKU</th>
                    <th>Jumlah Terjual</th>
                    <th>Pendapatan (IDR)</th>
                @else
                    <th>Kategori</th>
                    <th>Total (IDR)</th>
                    <th>Persentase</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
            <tr>
                @foreach($row as $cell)
                    <td>{{ is_numeric($cell) ? number_format($cell, 0, ',', '.') : $cell }}</td>
                @endforeach
            </tr>
            @empty
            <tr>
                <td colspan="{{ $type == 'inventory' ? 6 : ($type == 'sales' ? 4 : 3) }}" style="text-align: center;">
                    Tidak ada data
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
