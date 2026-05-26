<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fill existing NULL values with the first available area
        $firstArea = DB::table('master_area')->orderBy('id')->first();
        if ($firstArea) {
            DB::table('master_user')
                ->whereNull('master_area_id')
                ->update(['master_area_id' => $firstArea->id]);
        }

        Schema::table('master_user', function (Blueprint $table) {
            try {
                $table->dropForeign(['master_area_id']);
            } catch (\Exception $e) {}
        });

        Schema::table('master_user', function (Blueprint $table) {
            $table->unsignedBigInteger('master_area_id')->nullable(false)->change();
            $table->foreign('master_area_id')
                  ->references('id')->on('master_area')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('master_user', function (Blueprint $table) {
            try {
                $table->dropForeign(['master_area_id']);
            } catch (\Exception $e) {}
        });

        Schema::table('master_user', function (Blueprint $table) {
            $table->unsignedBigInteger('master_area_id')->nullable()->change();
            $table->foreign('master_area_id')
                  ->references('id')->on('master_area')
                  ->onDelete('set null');
        });
    }
};
