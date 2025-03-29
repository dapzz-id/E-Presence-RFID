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
        Schema::create('warga_tels', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("nis")->unique();
            $table->string("kelas");
            $table->text('foto_profile')->nullable();
            $table->text("alamat")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warga_tels');
    }
};
