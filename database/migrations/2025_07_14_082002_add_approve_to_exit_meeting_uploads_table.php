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
        Schema::table('exit_meeting_uploads', function (Blueprint $table) {
            $table->boolean('approve')->default(false)->after('approved_at_absensi');
            $table->timestamp('approve_at')->nullable()->after('approve');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exit_meeting_uploads', function (Blueprint $table) {
            $table->dropColumn('approve');
            $table->dropColumn('approve_at');
        });
    }
};
