<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom kode_produk & qty dipakai oleh form produk + ProdukController@store
     * namun belum pernah dibuat oleh migration manapun.
     */
    public function up(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            if (! Schema::hasColumn('produks', 'kode_produk')) {
                $table->string('kode_produk')->nullable()->after('nama_produk');
            }

            if (! Schema::hasColumn('produks', 'qty')) {
                $table->integer('qty')->default(0)->after('kode_produk');
            }
        });
    }

    public function down(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->dropColumn(['kode_produk', 'qty']);
        });
    }
};
