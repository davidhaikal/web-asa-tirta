<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembelians', function (Blueprint $table) {
            $table->id();

            $table->string('no_transaksi')->unique();

            // Disamakan dengan pola tabel penjualans: kolom teks, bukan FK
            $table->string('pelanggan')->nullable();

            $table->date('tanggal_pembelian');
            $table->date('tanggal_jatuh_tempo')->nullable();

            $table->decimal('total_harga', 15, 2)->default(0);
            $table->decimal('total_bayar', 15, 2)->default(0);

            $table->enum('status', ['lunas', 'utang'])->default('utang');

            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembelians');
    }
};