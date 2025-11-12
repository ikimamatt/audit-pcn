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
            // Add the new consolidated penyebab field
            $table->text('penyebab')->nullable()->after('permasalahan');
            
            // Drop the old separate root cause fields
            $table->dropColumn([
                'penyebab_people',
                'penyebab_process',
                'penyebab_policy',
                'penyebab_system',
                'penyebab_eksternal'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelaporan_temuan', function (Blueprint $table) {
            // Restore the old separate root cause fields
            $table->text('penyebab_people')->nullable()->after('permasalahan');
            $table->text('penyebab_process')->nullable()->after('penyebab_people');
            $table->text('penyebab_policy')->nullable()->after('penyebab_process');
            $table->text('penyebab_system')->nullable()->after('penyebab_policy');
            $table->text('penyebab_eksternal')->nullable()->after('penyebab_system');
            
            // Drop the consolidated penyebab field
            $table->dropColumn('penyebab');
        });
    }
};
