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
        Schema::table('realisasi_audits', function (Blueprint $table) {
            // Drop old foreign key targeting 'users' table
            $table->dropForeign(['approved_by']);
            
            // Create new foreign key targeting 'master_user' table
            $table->foreign('approved_by')
                  ->references('id')
                  ->on('master_user')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('realisasi_audits', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->foreign('approved_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }
};
