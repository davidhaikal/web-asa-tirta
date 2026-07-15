<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // create a generic test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // seed users for each role
        $this->call(UserSeeder::class);

        $this->seedSimpleTables();
    }

    private function seedSimpleTables(): void
    {
        // 1. Seed Produks
        $produkId = DB::table('produks')->insertGetId([
            'nama_produk' => 'Air Mineral',
            'harga' => 10000,
            'stok' => 100,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Seed Penjualans
        $penjualanId = DB::table('penjualans')->insertGetId([
            'kode' => 'PJ-' . time(),
            'tanggal' => now()->toDateString(),
            'total' => 50000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Seed Pengiriman
        DB::table('pengiriman')->insert([
            'penjualan_id' => $penjualanId,
            'tanggal_kirim' => now()->toDateString(),
            'status' => 'proses',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Seed Produksis
        $produksiId = DB::table('produksis')->insertGetId([
            'produk_id' => $produkId,
            'jumlah_produksi' => 100,
            'tanggal_produksi' => now()->toDateString(),
            'status' => 'selesai',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. Seed Qcs
        DB::table('qcs')->insert([
            'produksi_id' => $produksiId,
            'hasil' => 'Layak',
            'keterangan' => 'Sesuai standar',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 6. Seed Invoices
        DB::table('invoices')->insert([
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // 7. Seed Stoks
        DB::table('stoks')->insert([
            'produk_id' => $produkId,
            'jenis' => 'masuk',
            'jumlah' => 100,
            'keterangan' => 'Produksi awal',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
