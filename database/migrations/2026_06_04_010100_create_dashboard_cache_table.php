<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dashboard_cache', function (Blueprint $table) {
            $table->string('cache_key', 128)->primary();
            $table->longText('cache_data');
            $table->timestamp('refreshed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_cache');
    }
};
