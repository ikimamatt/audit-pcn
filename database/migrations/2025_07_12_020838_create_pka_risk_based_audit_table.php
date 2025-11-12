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
        Schema::create('pka_risk_based_audit', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_kerja_audit_id');
            $table->text('deskripsi_resiko');
            $table->text('penyebab_resiko');
            $table->text('dampak_resiko');
            $table->text('pengendalian_eksisting');
            $table->timestamps();

            $table->foreign('program_kerja_audit_id')->references('id')->on('program_kerja_audit')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pka_risk_based_audit');
    }
};
