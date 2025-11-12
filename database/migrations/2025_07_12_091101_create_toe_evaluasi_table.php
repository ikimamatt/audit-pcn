<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toe_evaluasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('toe_audit_id');
            $table->text('hasil_evaluasi');
            $table->timestamps();

            $table->foreign('toe_audit_id')->references('id')->on('toe_audit')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toe_evaluasi');
    }
}; 