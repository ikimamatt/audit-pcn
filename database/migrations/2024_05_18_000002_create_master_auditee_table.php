<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_auditee', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kd_bidang', 10)->unique();
            $table->string('nama_bidang');
            $table->boolean('is_available_for_up')->default(true)->comment('Apakah bidang ini tersedia untuk user UP');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_auditee');
    }
}; 