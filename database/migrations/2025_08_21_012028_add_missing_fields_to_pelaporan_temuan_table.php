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
        Schema::table('pelaporan_temuan', function (Blueprint $table) {
            // Add missing fields for comprehensive ISS data
            $table->text('permasalahan')->nullable()->after('hasil_temuan');
            $table->text('penyebab_people')->nullable()->after('permasalahan');
            $table->text('penyebab_process')->nullable()->after('penyebab_people');
            $table->text('penyebab_policy')->nullable()->after('penyebab_process');
            $table->text('penyebab_system')->nullable()->after('penyebab_policy');
            $table->text('penyebab_eksternal')->nullable()->after('penyebab_system');
            $table->text('kriteria')->nullable()->after('penyebab_eksternal');
            $table->text('dampak_terjadi')->nullable()->after('kriteria');
            $table->text('dampak_potensi')->nullable()->after('dampak_terjadi');
            $table->enum('signifikan', ['Tinggi', 'Medium', 'Rendah'])->nullable()->after('dampak_potensi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelaporan_temuan', function (Blueprint $table) {
            // Remove the added fields
            $table->dropColumn([
                'permasalahan',
                'penyebab_people',
                'penyebab_process',
                'penyebab_policy',
                'penyebab_system',
                'penyebab_eksternal',
                'kriteria',
                'dampak_terjadi',
                'dampak_potensi',
                'signifikan'
            ]);
        });
    }
};
