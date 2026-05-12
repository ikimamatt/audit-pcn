<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_unit', function (Blueprint $table) {
            $table->id();
            $table->string('kode_unit', 20)->unique();
            $table->string('nama_unit', 150);
            $table->timestamps();
        });

        // Tambahkan FK unit_id ke perencanaan_audit setelah master_unit sudah ada
        Schema::table('perencanaan_audit', function (Blueprint $table) {
            $table->foreign('unit_id')->references('id')->on('master_unit')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('perencanaan_audit', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
        });

        Schema::dropIfExists('master_unit');
    }
};
