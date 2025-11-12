<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tod_bpm_evaluasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tod_bpm_audit_id');
            $table->text('hasil_evaluasi');
            $table->timestamps();

            $table->foreign('tod_bpm_audit_id')->references('id')->on('tod_bpm_audit')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tod_bpm_evaluasi');
    }
};
