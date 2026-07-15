<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran_utangs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pembelian_id')
                ->constrained('pembelians')
                ->cascadeOnDelete();

            $table->date('tanggal_bayar');
            $table->decimal('jumlah_bayar', 15, 2);
            $table->text('keterangan')->nullable();

            // Siapa yang mencatat pembayaran (opsional, untuk audit trail)
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_utangs');
    }
};