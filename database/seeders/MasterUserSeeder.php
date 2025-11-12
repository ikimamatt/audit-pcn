<?php

// namespace Database\Seeders;

// use Illuminate\Database\Seeder;
// use Illuminate\Support\Facades\DB;

// class MasterUserSeeder extends Seeder
// {
//     public function run(): void
//     {
//         DB::table('master_user')->insert([
//             [
//                 'nama' => 'Ical KSPI',
//                 'nip' => '11111111',
//                 'master_akses_user_id' => 1,
//                 'created_at' => now(),
//                 'updated_at' => now(),
//             ],
//             [
//                 'nama' => 'Budi Auditor',
//                 'nip' => '22222222',
//                 'master_akses_user_id' => 2,
//                 'created_at' => now(),
//                 'updated_at' => now(),
//             ],
//             [
//                 'nama' => 'Sari PIC Auditee',
//                 'nip' => '33333333',
//                 'master_akses_user_id' => 3,
//                 'created_at' => now(),
//                 'updated_at' => now(),
//             ],
//             [
//                 'nama' => 'Dewi BOD',
//                 'nip' => '44444444',
//                 'master_akses_user_id' => 4,
//                 'created_at' => now(),
//                 'updated_at' => now(),
//             ],
//         ]);
//     }
// } 


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MasterUserSeeder extends Seeder
{
    public function run(): void
    {
        // Data dari gambar
        $users = [
            ['divisi' => 'SPI', 'nama' => 'KSPI', 'user' => 'KSPI_PCN', 'nip' => '84091962'],
            ['divisi' => 'SPI', 'nama' => 'ASMAN AUDIT', 'user' => 'ASMAN_AUDIT', 'nip' => '-'],
            ['divisi' => 'SPI', 'nama' => 'AUDITOR', 'user' => 'AUDITOR_PCN', 'nip' => '01253007PST'],
            ['divisi' => 'BOD', 'nama' => 'DIRUT', 'user' => 'DIRUT_PCN', 'nip' => '76020041CP'],
            ['divisi' => 'BOD', 'nama' => 'DIROP', 'user' => 'DIROP_PCN', 'nip' => '7705003F'],
            ['divisi' => 'BOD', 'nama' => 'DIRKAD', 'user' => 'DIRKAD_PCN', 'nip' => '7905004B'],
            ['divisi' => 'Divisi Operasi', 'nama' => 'Manager Operasi', 'user' => 'MAN_OPS', 'nip' => '6724001PRO'],
            ['divisi' => 'Divisi Operasi', 'nama' => 'Asman KSO', 'user' => 'ASMAN KSO_PCN', 'nip' => '7709066PKR'],
            ['divisi' => 'Divisi Operasi', 'nama' => 'Asman OPHAR', 'user' => 'ASMAN OPHAR_PCN', 'nip' => '8314001BJM'],
            ['divisi' => 'Divisi Renus', 'nama' => 'Manager Renus & IT', 'user' => 'MAN_REN', 'nip' => '8507283Z'],
            ['divisi' => 'Divisi Renus', 'nama' => 'Asman Renus', 'user' => 'ASMAN REN_PCN', 'nip' => '7605419HPI'],
            ['divisi' => 'Divisi Renus', 'nama' => 'Asman IT', 'user' => 'ASMAN IT_PCN', 'nip' => '92176132Y'],
            ['divisi' => 'SETPER', 'nama' => 'Sekretaris Perusahaan', 'user' => 'SEKPER_PCN', 'nip' => '-'],
            ['divisi' => 'SETPER', 'nama' => 'Spv Komunikasi & Tata Kelola', 'user' => 'SPV KTL_PCN', 'nip' => '7208028TRK'],
            ['divisi' => 'SETPER', 'nama' => 'Spv Hukum & Kepatuhan', 'user' => 'SPV HK_PCN', 'nip' => '94190370PST'],
            ['divisi' => 'SETPER', 'nama' => 'Spv Manriks', 'user' => 'SPV MRK_PCN', 'nip' => '8610007TRK'],
            ['divisi' => 'SETPER', 'nama' => 'Asman Lakdan', 'user' => 'ASMAN_LAKDAN', 'nip' => '6623001PRO'],
            ['divisi' => 'DIVISI Keuangan', 'nama' => 'Manager Keuangan', 'user' => 'MAN_KEU', 'nip' => '7510005TRK'],
            ['divisi' => 'DIVISI Keuangan', 'nama' => 'Asman Anggaran & Keuangan', 'user' => 'ASMAN KEU_PCN', 'nip' => '8010035TRK'],
            ['divisi' => 'DIVISI Keuangan', 'nama' => 'Asman Akuntansi & Pajak', 'user' => 'ASMAN AKT_PCN', 'nip' => '-'],
            ['divisi' => 'Divisi HC & Adm', 'nama' => 'Manager HC & Admum', 'user' => 'MAN_HC', 'nip' => '6924002PRO'],
            ['divisi' => 'Divisi HC & Adm', 'nama' => 'Asman Human Capital', 'user' => 'ASMAN HC_PCN', 'nip' => '8815012HPI'],
            ['divisi' => 'Divisi HC & Adm', 'nama' => 'Asman Admum', 'user' => 'ASMAN MUM_PCN', 'nip' => '8412522HPI'],
            ['divisi' => 'Cabang/site', 'nama' => 'Manager Cabang Kalbar', 'user' => 'MANCAB_KALBAR', 'nip' => '8608353HPI'],
            ['divisi' => 'Cabang/site', 'nama' => 'Manager Cabang Kaltimra', 'user' => 'MANCAB_KALTIMRA', 'nip' => '8013085PWK'],
            ['divisi' => 'Cabang/site', 'nama' => 'Manager Cabang Kalselteng', 'user' => 'MANCAB_KALSELTENG', 'nip' => '9414026HPI'],
            ['divisi' => 'Cabang/site', 'nama' => 'Manager Cabang Papua', 'user' => 'MANCAB_PAPUA', 'nip' => '9017011HPI'],
            ['divisi' => 'Cabang/site', 'nama' => 'Manager Site Berau', 'user' => 'MANSITE_BERAU', 'nip' => '8314005HPI'],
            ['divisi' => 'Cabang/site', 'nama' => 'Manager Site Samarinda', 'user' => 'MANSITE_SMD', 'nip' => '7711518HPI'],
            ['divisi' => 'Cabang/site', 'nama' => 'Manager Site Palangkaraya', 'user' => 'MANSITE_PKY', 'nip' => '8710458HPI'],
            ['divisi' => 'Cabang/site', 'nama' => 'Manager Site Singkawang', 'user' => 'MANSITE_SKW', 'nip' => '8006180SKW'],
            ['divisi' => 'Cabang/site', 'nama' => 'Manager Site NTB', 'user' => 'MANSITE_NTB', 'nip' => '91200385PST'],
            ['divisi' => 'Cabang/site', 'nama' => 'Manager Site NTT', 'user' => 'MANSITE_NTT', 'nip' => '9618001D2Y'],
            ['divisi' => 'Cabang/site', 'nama' => 'Manager Site Makassar', 'user' => 'MANSITE_MKS', 'nip' => '9317074B1P'],
            ['divisi' => 'Cabang/site', 'nama' => 'Manager Site Kendari', 'user' => 'MANSITE_KDR', 'nip' => '8515001KDR'],
            ['divisi' => 'Cabang/site', 'nama' => 'Manager Site Ambon', 'user' => 'MANSITE_AMB', 'nip' => '9017002ABN'],
            ['divisi' => 'Cabang/site', 'nama' => 'Manager Site Manado', 'user' => 'MANSITE_MND', 'nip' => '6323003PRO'],
        ];

        // Ambil mapping divisi ke id dari master_auditee (case-insensitive, trim)
        $divisiMap = DB::table('master_auditee')->get()->mapWithKeys(function($item) {
            return [strtolower(trim($item->divisi)) => $item->id];
        });

        foreach ($users as $user) {
            $divisiKey = strtolower(trim($user['divisi']));
            if (!isset($divisiMap[$divisiKey])) {
                echo "Divisi tidak ditemukan: " . $user['divisi'] . PHP_EOL;
                continue;
            }
            
            // Check if user already exists
            $existingUser = DB::table('master_user')->where('username', $user['user'])->first();
            if ($existingUser) {
                echo "User sudah ada: " . $user['user'] . PHP_EOL;
                continue;
            }
            
            DB::table('master_user')->insert([
                'nama' => $user['nama'],
                'username' => $user['user'],
                'nip' => $user['nip'],
                'password' => Hash::make('PCNJAYA123'),
                'master_auditee_id' => $divisiMap[$divisiKey],
            ]);
        }
    }
}