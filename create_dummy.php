<?php

$user = App\Models\User::first();
$produk = App\Models\Produk::first();

if (!$produk) {
    $produk = App\Models\Produk::create([
        'nama_produk' => 'Air Galon Dummy',
        'kode_produk' => 'AG-001',
        'qty' => 100,
        'stok' => 100,
        'harga' => 15000
    ]);
}

$penjualan = App\Models\Penjualan::create([
    'kode' => 'INV-TEST-001',
    'tanggal' => now(),
    'pelanggan' => 'Toko Makmur Sejahtera',
    'total' => 30000,
    'metode' => 'tunai',
    'status' => 'lunas',
    'user_id' => $user ? $user->id : 1
]);

App\Models\DetailPenjualan::create([
    'penjualan_id' => $penjualan->id,
    'produk_id' => $produk->id,
    'jumlah' => 2,
    'subtotal' => 30000
]);

App\Models\Pengiriman::create([
    'penjualan_id' => $penjualan->id,
    'tanggal_kirim' => now(),
    'status' => 'baru'
]);

echo "Data created successfully\n";
