<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('hari', function (Blueprint $table) {
            $table->id();
            $table->string('bulan', 50);
            $table->string('tahun', 4);
            $table->json('hari_produktif');
            $table->json('hari_tambahan')->nullable();
            $table->json('hari_libur')->nullable();
            $table->timestamps();
            
            // Index untuk pencarian lebih cepat
            $table->index(['tahun', 'bulan']);
            
            // Memastikan kombinasi bulan dan tahun unik
            $table->unique(['bulan', 'tahun']);
        });

    }

    public function down()
    {
        Schema::dropIfExists('hari');
    }
};