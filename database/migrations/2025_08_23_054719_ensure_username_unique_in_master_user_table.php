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
            // Ensure username field exists and is unique
            if (Schema::hasColumn('master_user', 'username')) {
                $table->string('username')->unique()->change();
            } else {
                $table->string('username')->unique();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_user', function (Blueprint $table) {
            // Remove unique constraint from username
            $table->dropUnique(['username']);
        });
    }
};
