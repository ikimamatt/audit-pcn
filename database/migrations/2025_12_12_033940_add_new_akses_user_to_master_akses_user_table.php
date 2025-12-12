<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $newAkses = [
            'ASMAN KSPI',
            'Manager',
            'Assistant Manager',
        ];

        foreach ($newAkses as $akses) {
            DB::table('master_akses_user')->updateOrInsert(
                ['nama_akses' => $akses],
                ['nama_akses' => $akses]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $newAkses = [
            'ASMAN KSPI',
            'Manager',
            'Assistant Manager',
        ];

        DB::table('master_akses_user')->whereIn('nama_akses', $newAkses)->delete();
    }
};
