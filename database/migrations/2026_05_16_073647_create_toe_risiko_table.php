<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toe_risiko', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('toe_audit_id');
            $table->unsignedBigInteger('pka_risiko_id');
            $table->timestamps();

            $table->foreign('toe_audit_id')->references('id')->on('toe_audit')->onDelete('cascade');
            $table->foreign('pka_risiko_id')->references('id')->on('pka_risiko')->onDelete('cascade');

            $table->unique(['toe_audit_id', 'pka_risiko_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toe_risiko');
    }
};
