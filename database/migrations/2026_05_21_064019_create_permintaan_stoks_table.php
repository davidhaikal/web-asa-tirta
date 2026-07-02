<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    // Run the migrations.

    public function up(): void
    {
        Schema::create('permintaan_stoks', function (Blueprint $table) {

            $table->id();
            $table->foreignId('produk_id');
            $table->integer('jumlah');
            $table->string('status')->default('Menunggu');
            $table->date('tanggal');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_stoks');
    }
};
