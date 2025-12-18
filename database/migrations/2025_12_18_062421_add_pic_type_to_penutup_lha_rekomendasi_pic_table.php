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
        Schema::table('penutup_lha_rekomendasi_pic', function (Blueprint $table) {
            $table->enum('pic_type', ['business_contact', 'approval_1_spi', 'approval_2_spi'])->after('master_user_id')->default('business_contact');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penutup_lha_rekomendasi_pic', function (Blueprint $table) {
            $table->dropColumn('pic_type');
        });
    }
};
