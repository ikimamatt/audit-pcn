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
        Schema::table('entry_meeting', function (Blueprint $table) {
            $table->unsignedBigInteger('program_kerja_audit_id')->nullable()->after('auditee_id');
            $table->date('actual_meeting_date')->nullable()->after('tanggal');
            $table->foreign('program_kerja_audit_id')->references('id')->on('program_kerja_audit')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entry_meeting', function (Blueprint $table) {
            $table->dropForeign(['program_kerja_audit_id']);
            $table->dropColumn(['program_kerja_audit_id', 'actual_meeting_date']);
        });
    }
};
