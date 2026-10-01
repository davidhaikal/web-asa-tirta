<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel invoices tadinya kosong (hanya id + timestamps).
     * Kolom diisi saat transaksi lunas (KasirController@bayarTransaksi).
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('invoice_no')->unique()->nullable()->after('id');
            $table->foreignId('penjualan_id')->nullable()->constrained('penjualans')->cascadeOnDelete();
            $table->string('pelanggan')->nullable();
            $table->decimal('total', 15, 2)->default(0);
            $table->date('tanggal')->nullable();
            $table->string('status')->default('terbit');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['penjualan_id']);
            $table->dropColumn(['invoice_no', 'penjualan_id', 'pelanggan', 'total', 'tanggal', 'status']);
        });
    }
};
