<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterAuditeeSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus data dan reset auto-increment tanpa melanggar foreign key constraint
        DB::table('master_auditee')->delete();
        DB::statement('ALTER TABLE master_auditee AUTO_INCREMENT = 1');
        DB::table('master_auditee')->insert([
            ['divisi' => 'SPI'],
            ['divisi' => 'BOD'],
            ['divisi' => 'Divisi Operasi'],
            ['divisi' => 'Divisi Renus'],
            ['divisi' => 'SETPER'],
            ['divisi' => 'DIVISI Keuangan'],
            ['divisi' => 'Divisi HC & Adm'],
            ['divisi' => 'Cabang/site'],
            ['divisi' => 'Cabang Kalbar'],
            ['divisi' => 'Cabang Kaltimra'],
            ['divisi' => 'Cabang Kalselteng'],
            ['divisi' => 'Cabang Papua'],
            ['divisi' => 'Manager Site Berau'],
            ['divisi' => 'Manager Site Samarinda'],
            ['divisi' => 'Site Palangkaraya'],
            ['divisi' => 'Site Singkawang'],
            ['divisi' => 'Site NTB'],
            ['divisi' => 'Site NTT'],
            ['divisi' => 'Site Makassar'],
            ['divisi' => 'Site Kendari'],
            ['divisi' => 'Site Ambon'],
            ['divisi' => 'Site Manado'],
        ]);
    }
}
