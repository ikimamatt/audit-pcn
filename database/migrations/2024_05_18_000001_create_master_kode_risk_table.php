<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_kode_risk', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kelompok_risiko');
            $table->string('kode_risiko');
            $table->string('kelompok_risiko_detail');
            $table->text('deskripsi_risiko');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_kode_risk');
    }
}; 