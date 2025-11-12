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
        Schema::create('monitoring_tindak_lanjut', function (Blueprint $table) {
            $table->id();
            $table->string('objek_pemeriksaan');
            $table->integer('aoi_count')->default(0);
            $table->integer('rekomendasi_count')->default(0);
            $table->integer('tindak_lanjut_target')->default(0);
            $table->integer('tindak_lanjut_real')->default(0);
            $table->integer('sisa_target')->default(0);
            $table->integer('sisa_real')->default(0);
            
            // Data bulanan
            $table->integer('bulan_jan_target')->default(0);
            $table->integer('bulan_jan_real')->default(0);
            $table->integer('bulan_feb_target')->default(0);
            $table->integer('bulan_feb_real')->default(0);
            $table->integer('bulan_mar_target')->default(0);
            $table->integer('bulan_mar_real')->default(0);
            $table->integer('bulan_apr_target')->default(0);
            $table->integer('bulan_apr_real')->default(0);
            $table->integer('bulan_mei_target')->default(0);
            $table->integer('bulan_mei_real')->default(0);
            $table->integer('bulan_jun_target')->default(0);
            $table->integer('bulan_jun_real')->default(0);
            $table->integer('bulan_jul_target')->default(0);
            $table->integer('bulan_jul_real')->default(0);
            $table->integer('bulan_ags_target')->default(0);
            $table->integer('bulan_ags_real')->default(0);
            $table->integer('bulan_sep_target')->default(0);
            $table->integer('bulan_sep_real')->default(0);
            $table->integer('bulan_okt_target')->default(0);
            $table->integer('bulan_okt_real')->default(0);
            
            $table->boolean('is_category')->default(false);
            $table->boolean('is_total')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring_tindak_lanjut');
    }
};
