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
        Schema::create('program_kerja_audit', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('perencanaan_audit_id'); // relasi ke surat tugas
            $table->date('tanggal_pka');
            $table->string('no_pka');
            $table->text('informasi_umum')->nullable();
            $table->text('kpi_tidak_tercapai')->nullable();
            $table->text('data_awal_dokumen')->nullable();
            $table->timestamps();

            $table->foreign('perencanaan_audit_id')->references('id')->on('perencanaan_audit')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_kerja_audit');
    }
};
