<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pka_proses_bisnis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('program_kerja_audit_id');
            $table->text('nama_proses_bisnis');
            $table->unsignedInteger('urutan')->default(1);
            $table->timestamps();

            $table->foreign('program_kerja_audit_id')
                  ->references('id')
                  ->on('program_kerja_audit')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pka_proses_bisnis');
    }
};
