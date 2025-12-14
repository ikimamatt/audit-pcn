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
        Schema::table('pelaporan_hasil_audit', function (Blueprint $table) {
            $table->dropColumn('po_audit_konsul');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelaporan_hasil_audit', function (Blueprint $table) {
            $table->enum('po_audit_konsul', ['PO AUDIT', 'KONSUL'])->after('jenis_lha_lhk');
        });
    }
};

