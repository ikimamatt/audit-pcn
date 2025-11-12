<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_akses_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama_akses');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_akses_user');
    }
}; 