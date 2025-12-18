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
        // Data divisi sesuai dengan gambar yang diberikan
        DB::table('master_auditee')->insert([
            ['divisi' => 'SPI'],
            ['divisi' => 'KEUANGAN'],
            ['divisi' => 'RENUS IT'],
            ['divisi' => 'OPERASI'],
            ['divisi' => 'HUMAN CAPITAL'],
            ['divisi' => 'SEKPER'],
            ['divisi' => 'BOD'],
            ['divisi' => 'SUPER ADMIN'],
            ['divisi' => 'CABANG KALTIMRA'],
        ]);
    }
}
