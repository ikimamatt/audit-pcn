<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add master_area_id column if it does not exist
        if (!Schema::hasColumn('master_user', 'master_area_id')) {
            Schema::table('master_user', function (Blueprint $table) {
                $table->integer('master_area_id')->nullable()->after('master_auditee_id');
            });
        }

        // 2. Fill the column with a default master_area id (first one)
        $firstArea = DB::table('master_area')->orderBy('id')->first();
        if ($firstArea) {
            DB::table('master_user')
                ->whereNull('master_area_id')
                ->update(['master_area_id' => $firstArea->id]);
        }

        // 3. Drop old FK and column if it still exists
        if (Schema::hasColumn('master_user', 'master_unit_id')) {
            Schema::table('master_user', function (Blueprint $table) {
                try {
                    $table->dropForeign(['master_unit_id']);
                } catch (\Exception $e) {
                    // Ignore if FK doesn't exist
                }
                $table->dropColumn('master_unit_id');
            });
        }

        // 4. Change master_area_id to NOT NULL and add foreign constraint
        Schema::table('master_user', function (Blueprint $table) {
            $table->integer('master_area_id')->nullable(false)->change();
            
            try {
                $table->foreign('master_area_id')
                      ->references('id')->on('master_area')
                      ->onDelete('restrict');
            } catch (\Exception $e) {
                // Ignore if FK already exists or cannot be created
            }
        });
    }

    public function down(): void
    {
        Schema::table('master_user', function (Blueprint $table) {
            try {
                $table->dropForeign(['master_area_id']);
            } catch (\Exception $e) {
                // Ignore if FK doesn't exist
            }
            $table->dropColumn('master_area_id');

            // Re-create master_unit_id
            $table->unsignedBigInteger('master_unit_id')->nullable()->after('master_auditee_id');
        });

        // Fill master_unit_id with a default if exists
        $firstUnit = DB::table('master_unit')->orderBy('id')->first();
        if ($firstUnit) {
            DB::table('master_user')
                ->whereNull('master_unit_id')
                ->update(['master_unit_id' => $firstUnit->id]);
        }

        Schema::table('master_user', function (Blueprint $table) {
            $table->unsignedBigInteger('master_unit_id')->nullable(false)->change();
            $table->foreign('master_unit_id')
                  ->references('id')->on('master_unit')
                  ->onDelete('restrict');
        });
    }
};
