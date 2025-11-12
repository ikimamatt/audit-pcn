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
        Schema::table('master_auditee', function (Blueprint $table) {
            $table->string('direktorat')->nullable()->after('id');
            $table->string('divisi_cabang')->nullable()->after('direktorat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_auditee', function (Blueprint $table) {
            $table->dropColumn(['direktorat', 'divisi_cabang']);
        });
    }
};
