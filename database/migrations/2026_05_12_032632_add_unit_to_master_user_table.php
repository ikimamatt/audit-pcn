<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_user', function (Blueprint $table) {
            $table->uuid('master_area_id')->nullable()->after('master_auditee_id');
            $table->foreign('master_area_id')
                  ->references('id')->on('master_area')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('master_user', function (Blueprint $table) {
            try {
                $table->dropForeign(['master_area_id']);
            } catch (\Exception $e) {}
            $table->dropColumn('master_area_id');
        });
    }
};
