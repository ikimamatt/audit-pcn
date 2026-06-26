<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tod_bpm_risiko', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tod_bpm_audit_id');
            $table->uuid('pka_risiko_id');
            $table->timestamps();

            $table->foreign('tod_bpm_audit_id')->references('id')->on('tod_bpm_audit')->onDelete('cascade');
            $table->foreign('pka_risiko_id')->references('id')->on('pka_risiko')->onDelete('cascade');

            $table->unique(['tod_bpm_audit_id', 'pka_risiko_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tod_bpm_risiko');
    }
};
