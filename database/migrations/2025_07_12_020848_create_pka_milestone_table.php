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
        Schema::create('pka_milestone', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_kerja_audit_id');
            $table->string('nama_milestone');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->timestamps();

            $table->foreign('program_kerja_audit_id')->references('id')->on('program_kerja_audit')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pka_milestone');
    }
};
