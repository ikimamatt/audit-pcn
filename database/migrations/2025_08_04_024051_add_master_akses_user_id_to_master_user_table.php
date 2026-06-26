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
            // Tambahkan kolom master_akses_user_id sebagai nullable UUID
            $table->uuid('master_akses_user_id')->nullable()->after('master_auditee_id');
        });

        // Tambahkan foreign key constraint
        Schema::table('master_user', function (Blueprint $table) {
            $table->foreign('master_akses_user_id')->references('id')->on('master_akses_user')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_user', function (Blueprint $table) {
            $table->dropForeign(['master_akses_user_id']);
            $table->dropColumn('master_akses_user_id');
        });
    }
};
