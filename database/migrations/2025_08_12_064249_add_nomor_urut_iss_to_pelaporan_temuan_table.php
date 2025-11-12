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
            $table->unsignedInteger('nomor_urut_iss')->after('id')->comment('Nomor urut untuk generate nomor ISS');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelaporan_temuan', function (Blueprint $table) {
            $table->dropColumn('nomor_urut_iss');
        });
    }
};
