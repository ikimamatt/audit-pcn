<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pka_kontrol', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pka_risiko_id');
            $table->text('deskripsi_kontrol');
            $table->unsignedInteger('urutan')->default(1);
            $table->timestamps();

            $table->foreign('pka_risiko_id')
                  ->references('id')
                  ->on('pka_risiko')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pka_kontrol');
    }
};
