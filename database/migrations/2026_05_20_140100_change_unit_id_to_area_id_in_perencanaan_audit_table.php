<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perencanaan_audit', function (Blueprint $table) {
            // Drop foreign key and column unit_id
            $table->dropForeign(['unit_id']);
            $table->dropColumn('unit_id');

            // Add area_id (integer matches master_area.id, nullable)
            $table->integer('area_id')->nullable()->after('jenis_audit_id');
            $table->foreign('area_id')
                  ->references('id')->on('master_area')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('perencanaan_audit', function (Blueprint $table) {
            // Drop area_id FK and column
            $table->dropForeign(['area_id']);
            $table->dropColumn('area_id');

            // Restore unit_id (unsignedBigInteger matches master_unit.id, nullable)
            $table->unsignedBigInteger('unit_id')->nullable()->after('jenis_audit_id');
            $table->foreign('unit_id')
                  ->references('id')->on('master_unit')
                  ->onDelete('set null');
        });
    }
};
