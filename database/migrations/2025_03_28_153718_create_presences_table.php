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
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->string('nis');
            $table->foreign('nis')->references('nis')->on('warga_tels')->onUpdate('cascade');
            $table->dateTime('time_masuk')->default(now());
            $table->dateTime('time_keluar')->nullable();
            $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Alpa', 'Terlambat'])->default('Hadir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presences');
    }
};
