<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterAksesUserSeeder extends Seeder
{
    public function run(): void
    {
        $aksesList = [
            'KSPI',
            'Auditor',
            'PIC Auditee',
            'BOD',
            'ASMAN KSPI',
            'Manager',
            'Assistant Manager',
        ];

        foreach ($aksesList as $akses) {
            DB::table('master_akses_user')->updateOrInsert(
                ['nama_akses' => $akses],
                ['nama_akses' => $akses]
            );
        }
    }
} 