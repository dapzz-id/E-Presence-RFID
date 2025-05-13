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
        Schema::create('late_entries', function (Blueprint $table) {
            $table->id();
            $table->string('nis');
            $table->string('rfid_id');
            $table->dateTime('time');
            $table->enum('type', ['Presensi Masuk', 'Presensi Keluar']);
            $table->string('token')->unique();
            $table->timestamps();
        
            $table->foreign('nis')->references('nis')->on('warga_tels')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('rfid_id')->references('rfid_id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });               
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('late_entries');
    }
};
