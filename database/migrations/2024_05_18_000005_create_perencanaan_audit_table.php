<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perencanaan_audit', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('tanggal_surat_tugas');
            $table->string('nomor_surat_tugas');
            $table->string('jenis_audit');
            $table->uuid('koordinator_id')->nullable();
            $table->uuid('ketua_tim_id')->nullable();
            $table->uuid('unit_id')->nullable();
            $table->json('auditor');
            $table->uuid('auditee_id');
            $table->json('ruang_lingkup');
            $table->date('tanggal_audit_mulai');
            $table->date('tanggal_audit_sampai');
            $table->string('periode_audit');
            $table->timestamps();

            $table->foreign('auditee_id')->references('id')->on('master_auditee')->onDelete('restrict');
            $table->foreign('koordinator_id')->references('id')->on('master_user')->onDelete('restrict');
            $table->foreign('ketua_tim_id')->references('id')->on('master_user')->onDelete('restrict');
            // unit_id FK ditambahkan di migration 2026_05_12_031458 (setelah master_unit dibuat)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perencanaan_audit');
    }
}; 