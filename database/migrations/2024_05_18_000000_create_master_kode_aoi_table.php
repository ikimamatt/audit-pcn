<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_kode_aoi', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('indikator_pengawasan');
            $table->string('kode_area_of_improvement');
            $table->text('deskripsi_area_of_improvement');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_kode_aoi');
    }
}; 