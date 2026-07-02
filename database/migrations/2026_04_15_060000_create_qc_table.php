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
        Schema::create('qcs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produksi_id')->constrained();
            $table->enum('hasil', ['Layak','Tidak Layak']);
            $table->text('keterangan')->nullable();
            $table->integer('jumlah_dicek')->default(0);
            $table->date('tanggal_qc')->nullable();
            $table->string('foto_reject')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qcs');
    }
};
