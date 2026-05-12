<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fill existing NULL values with the first available unit
        $firstUnit = DB::table('master_unit')->orderBy('id')->first();
        if ($firstUnit) {
            DB::table('master_user')
                ->whereNull('master_unit_id')
                ->update(['master_unit_id' => $firstUnit->id]);
        }

        Schema::table('master_user', function (Blueprint $table) {
            // Drop old FK (was created with nullOnDelete)
            $table->dropForeign(['master_unit_id']);
        });

        Schema::table('master_user', function (Blueprint $table) {
            // Make NOT NULL then recreate FK with cascade delete (no SET NULL)
            $table->unsignedBigInteger('master_unit_id')->nullable(false)->change();
            $table->foreign('master_unit_id')
                  ->references('id')->on('master_unit')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('master_user', function (Blueprint $table) {
            $table->dropForeign(['master_unit_id']);
        });

        Schema::table('master_user', function (Blueprint $table) {
            $table->unsignedBigInteger('master_unit_id')->nullable()->change();
            $table->foreign('master_unit_id')
                  ->references('id')->on('master_unit')
                  ->onDelete('set null');
        });
    }
};
