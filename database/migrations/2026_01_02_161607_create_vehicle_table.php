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
        Schema::create('vehicle', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kendaraan');
            $table->string('jenis_kendaraan');
            $table->string('nomor_polisi')->unique();
            $table->string('warna_kendaraan');
            $table->string('bahan_bakar');
            $table->integer('kapasitas_penumpang');
            $table->enum('status_ketersediaan', ['tersedia', 'dipinjam', 'maintenance'])->default('tersedia');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle');
    }
};
