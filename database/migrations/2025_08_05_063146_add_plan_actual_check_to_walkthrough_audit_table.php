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
        Schema::table('walkthrough_audit', function (Blueprint $table) {
            // Tambah kolom untuk plan check dan actual check
            $table->unsignedBigInteger('program_kerja_audit_id')->nullable()->after('perencanaan_audit_id');
            $table->date('planned_walkthrough_date')->nullable()->after('tanggal_walkthrough');
            $table->date('actual_walkthrough_date')->nullable()->after('planned_walkthrough_date');
            
            // Tambah foreign key untuk program_kerja_audit_id
            $table->foreign('program_kerja_audit_id')->references('id')->on('program_kerja_audit')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('walkthrough_audit', function (Blueprint $table) {
            $table->dropForeign(['program_kerja_audit_id']);
            $table->dropColumn(['program_kerja_audit_id', 'planned_walkthrough_date', 'actual_walkthrough_date']);
        });
    }
};
