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
        Schema::table('penutup_lha_rekomendasi', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['pelaporan_isi_lha_id']);
            
            // Add new foreign key constraint to pelaporan_temuan
            $table->foreign('pelaporan_isi_lha_id')->references('id')->on('pelaporan_temuan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penutup_lha_rekomendasi', function (Blueprint $table) {
            // Drop the new foreign key constraint
            $table->dropForeign(['pelaporan_isi_lha_id']);
            
            // Restore the original foreign key constraint to pelaporan_isi_lha
            $table->foreign('pelaporan_isi_lha_id')->references('id')->on('pelaporan_isi_lha')->onDelete('cascade');
        });
    }
};
