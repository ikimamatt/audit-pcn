<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pka_risiko', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pka_proses_bisnis_id');
            $table->text('deskripsi_risiko');
            $table->string('level_risiko')->nullable();
            $table->text('penyebab_risiko')->nullable();
            $table->text('dampak_risiko')->nullable();
            $table->unsignedInteger('urutan')->default(1);
            $table->timestamps();

            $table->foreign('pka_proses_bisnis_id')
                  ->references('id')
                  ->on('pka_proses_bisnis')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pka_risiko');
    }
};
