<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom ke penjualans
        Schema::table('penjualans', function (Blueprint $table) {
            $table->string('kode')->unique()->after('id');
            $table->string('pelanggan')->nullable()->after('tanggal');
            $table->enum('metode', ['tunai', 'transfer', 'qris'])->default('tunai')->after('total');
            $table->enum('status', ['lunas', 'pending', 'batal'])->default('lunas')->after('metode');
            $table->foreignId('user_id')->nullable()->constrained()->after('status');
        });

        // Tabel detail penjualan
        Schema::create('detail_penjualans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penjualan_id')->constrained('penjualans')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produks');
            $table->integer('jumlah');
            $table->integer('subtotal');
            $table->timestamps();
        });

        // Tabel Purchase Order (PO) — kebutuhan barang dari kasir/marketing
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('kode_po')->unique();
            $table->foreignId('produk_id')->constrained('produks');
            $table->integer('jumlah');
            $table->date('tanggal_butuh');
            $table->string('bulan_produksi')->nullable();
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak', 'selesai'])->default('menunggu');
            $table->text('catatan')->nullable();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('detail_penjualans');
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['kode', 'pelanggan', 'metode', 'status', 'user_id']);
        });
    }
};