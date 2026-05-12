<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_user', function (Blueprint $table) {
            $table->foreignId('master_unit_id')
                  ->nullable()
                  ->after('master_auditee_id')
                  ->constrained('master_unit')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('master_user', function (Blueprint $table) {
            $table->dropForeign(['master_unit_id']);
            $table->dropColumn('master_unit_id');
        });
    }
};
