<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perencanaan_audit', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('tanggal_surat_tugas');
            $table->string('nomor_surat_tugas');
            $table->string('jenis_audit');
            $table->json('auditor');
            $table->unsignedBigInteger('auditee_id');
            $table->json('ruang_lingkup');
            $table->date('tanggal_audit_mulai');
            $table->date('tanggal_audit_sampai');
            $table->string('periode_audit');
            $table->timestamps();

            $table->foreign('auditee_id')->references('id')->on('master_auditee')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perencanaan_audit');
    }
}; 