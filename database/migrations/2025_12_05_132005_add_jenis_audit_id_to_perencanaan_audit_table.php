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
        Schema::table('perencanaan_audit', function (Blueprint $table) {
            $table->unsignedBigInteger('jenis_audit_id')->nullable()->after('nomor_surat_tugas');
            $table->foreign('jenis_audit_id')->references('id')->on('master_jenis_audit')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perencanaan_audit', function (Blueprint $table) {
            $table->dropForeign(['jenis_audit_id']);
            $table->dropColumn('jenis_audit_id');
        });
    }
};
