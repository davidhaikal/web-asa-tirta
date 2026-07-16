<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->integer('qty')->default(0)->after('harga');
        });

        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->integer('qty')->default(0)->after('produk_id');
        });

        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->integer('qty')->default(0)->after('produk_id');
        });

        Schema::table('barang_rusaks', function (Blueprint $table) {
            $table->integer('qty')->default(0)->after('produk_id');
        });

        Schema::table('permintaan_stoks', function (Blueprint $table) {
            $table->integer('qty')->default(0)->after('produk_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->dropColumn('qty');
        });

        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->dropColumn('qty');
        });

        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->dropColumn('qty');
        });

        Schema::table('barang_rusaks', function (Blueprint $table) {
            $table->dropColumn('qty');
        });

        Schema::table('permintaan_stoks', function (Blueprint $table) {
            $table->dropColumn('qty');
        });
    }
};
