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
        Schema::create('jadwal_pkpt_audits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('auditee_id');
            $table->string('jenis_audit');
            $table->integer('jumlah_auditor');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('status_approval', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('auditee_id')->references('id')->on('master_auditee')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_pkpt_audits');
    }
};
