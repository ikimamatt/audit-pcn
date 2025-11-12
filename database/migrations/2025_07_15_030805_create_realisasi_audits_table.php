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
        Schema::create('realisasi_audits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('perencanaan_audit_id');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->enum('status', ['selesai', 'on progress', 'belum']);
            $table->timestamps();

            $table->foreign('perencanaan_audit_id')->references('id')->on('perencanaan_audit')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('realisasi_audits');
    }
};
