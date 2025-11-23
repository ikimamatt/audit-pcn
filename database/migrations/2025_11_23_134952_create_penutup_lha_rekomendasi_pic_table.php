<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('penutup_lha_rekomendasi_pic', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penutup_lha_rekomendasi_id')->constrained('penutup_lha_rekomendasi')->onDelete('cascade');
            $table->foreignId('master_user_id')->constrained('master_user')->onDelete('cascade');
            $table->timestamps();
            
            // Ensure unique combination
            $table->unique(['penutup_lha_rekomendasi_id', 'master_user_id'], 'penutup_lha_rekomendasi_pic_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penutup_lha_rekomendasi_pic');
    }
};
