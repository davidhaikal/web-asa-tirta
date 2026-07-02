<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_keluar', function (Blueprint $table) {

            $table->id();

            $table->foreignId('produk_id');
            $table->integer('jumlah');
            $table->date('tanggal_keluar');
            $table->text('tujuan')->nullable();
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_keluar');
    }
};