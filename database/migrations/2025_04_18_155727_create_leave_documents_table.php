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
        Schema::create('leave_documents', function (Blueprint $table) {
            $table->id();
            $table->string('nis');
            $table->foreign('nis')->references('nis')->on('warga_tels')->onUpdate('cascade')->onDelete('cascade');
            $table->enum('type', ['Izin', 'Sakit']);
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason')->nullable();
            $table->longText('document_path')->nullable();
            $table->timestamps();
            
            // Add index for faster lookups
            $table->index(['nis', 'start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_documents');
    }
};