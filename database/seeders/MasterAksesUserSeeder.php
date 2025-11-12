<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterAksesUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('master_akses_user')->insert([
            ['nama_akses' => 'KSPI'],
            ['nama_akses' => 'Auditor'],
            ['nama_akses' => 'PIC Auditee'],
            ['nama_akses' => 'BOD'],
        ]);
    }
} 