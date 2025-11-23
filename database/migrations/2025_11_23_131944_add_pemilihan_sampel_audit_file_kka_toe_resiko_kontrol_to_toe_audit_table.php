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
        Schema::table('toe_audit', function (Blueprint $table) {
            $table->text('pemilihan_sampel_audit')->nullable()->after('pengendalian_eksisting');
            $table->text('resiko')->nullable()->after('pemilihan_sampel_audit');
            $table->text('kontrol')->nullable()->after('resiko');
            $table->string('file_kka_toe')->nullable()->after('kontrol');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('toe_audit', function (Blueprint $table) {
            $table->dropColumn(['pemilihan_sampel_audit', 'resiko', 'kontrol', 'file_kka_toe']);
        });
    }
};
