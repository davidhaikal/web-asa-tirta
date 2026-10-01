<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tracking penagihan riil pada transaksi penjualan
     * (aksi kirim tagihan / tagih di PenagihanController).
     */
    public function up(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->timestamp('tagihan_dikirim_at')->nullable()->after('user_id');
            $table->timestamp('tagihan_ditagih_at')->nullable()->after('tagihan_dikirim_at');
        });
    }

    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn(['tagihan_dikirim_at', 'tagihan_ditagih_at']);
        });
    }
};
