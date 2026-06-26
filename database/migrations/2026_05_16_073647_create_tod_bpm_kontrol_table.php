<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tod_bpm_kontrol', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tod_bpm_audit_id');
            $table->uuid('pka_kontrol_id');
            $table->timestamps();

            $table->foreign('tod_bpm_audit_id')->references('id')->on('tod_bpm_audit')->onDelete('cascade');
            $table->foreign('pka_kontrol_id')->references('id')->on('pka_kontrol')->onDelete('cascade');

            $table->unique(['tod_bpm_audit_id', 'pka_kontrol_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tod_bpm_kontrol');
    }
};
