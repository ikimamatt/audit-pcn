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
        Schema::table('master_user', function (Blueprint $table) {
            $table->string('email')->nullable()->after('nip');
            $table->string('no_telpon')->nullable()->after('email');
            $table->string('jabatan')->nullable()->after('no_telpon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_user', function (Blueprint $table) {
            $table->dropColumn(['email', 'no_telpon', 'jabatan']);
        });
    }
};
