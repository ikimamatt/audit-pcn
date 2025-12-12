<?php

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

        // 5 Divisi yang dipilih untuk user PIC Auditee, Manager, dan Assistant Manager
        $selectedDivisi = [
            'Divisi Operasi',
            'Divisi Renus',
            'SETPER',
            'DIVISI Keuangan',
            'Divisi HC & Adm',
        ];

        // Ambil akses ID
        $picAuditeeAkses = DB::table('master_akses_user')->where('nama_akses', 'PIC Auditee')->first();
        $managerAkses = DB::table('master_akses_user')->where('nama_akses', 'Manager')->first();
        $assistantManagerAkses = DB::table('master_akses_user')->where('nama_akses', 'Assistant Manager')->first();
        
        if (!$picAuditeeAkses) {
            echo "Akses 'PIC Auditee' tidak ditemukan!" . PHP_EOL;
        }
        if (!$managerAkses) {
            echo "Akses 'Manager' tidak ditemukan!" . PHP_EOL;
        }
        if (!$assistantManagerAkses) {
            echo "Akses 'Assistant Manager' tidak ditemukan!" . PHP_EOL;
        }

        // Tambahkan 5 user dengan akses PIC Auditee (masing-masing ke 5 divisi yang berbeda)
        $picAuditeeUsers = [
            ['divisi' => 'Divisi Operasi', 'nama' => 'PIC Auditee Operasi', 'user' => 'PIC_OPS_01', 'nip' => '9001001OPS'],
            ['divisi' => 'Divisi Renus', 'nama' => 'PIC Auditee Renus', 'user' => 'PIC_REN_01', 'nip' => '9002001REN'],
            ['divisi' => 'SETPER', 'nama' => 'PIC Auditee SETPER', 'user' => 'PIC_SET_01', 'nip' => '9003001SET'],
            ['divisi' => 'DIVISI Keuangan', 'nama' => 'PIC Auditee Keuangan', 'user' => 'PIC_KEU_01', 'nip' => '9004001KEU'],
            ['divisi' => 'Divisi HC & Adm', 'nama' => 'PIC Auditee HC', 'user' => 'PIC_HC_01', 'nip' => '9005001HC'],
        ];

        foreach ($picAuditeeUsers as $picUser) {
            $divisiKey = strtolower(trim($picUser['divisi']));
            if (!isset($divisiMap[$divisiKey])) {
                echo "Divisi tidak ditemukan: " . $picUser['divisi'] . PHP_EOL;
                continue;
            }
            
            // Check if user already exists
            $existingUser = DB::table('master_user')->where('username', $picUser['user'])->first();
            if ($existingUser) {
                echo "User sudah ada: " . $picUser['user'] . PHP_EOL;
                continue;
            }
            
            if (!$picAuditeeAkses) {
                continue;
            }
            
            DB::table('master_user')->insert([
                'nama' => $picUser['nama'],
                'username' => $picUser['user'],
                'nip' => $picUser['nip'],
                'password' => Hash::make('PCNJAYA123'),
                'master_auditee_id' => $divisiMap[$divisiKey],
                'master_akses_user_id' => $picAuditeeAkses->id,
            ]);
        }

        // Tambahkan 5 user dengan akses Manager (masing-masing ke 5 divisi yang sama)
        $managerUsers = [
            ['divisi' => 'Divisi Operasi', 'nama' => 'Manager Operasi', 'user' => 'MAN_OPS_01', 'nip' => '9101001MGR'],
            ['divisi' => 'Divisi Renus', 'nama' => 'Manager Renus', 'user' => 'MAN_REN_01', 'nip' => '9102001MGR'],
            ['divisi' => 'SETPER', 'nama' => 'Manager SETPER', 'user' => 'MAN_SET_01', 'nip' => '9103001MGR'],
            ['divisi' => 'DIVISI Keuangan', 'nama' => 'Manager Keuangan', 'user' => 'MAN_KEU_01', 'nip' => '9104001MGR'],
            ['divisi' => 'Divisi HC & Adm', 'nama' => 'Manager HC', 'user' => 'MAN_HC_01', 'nip' => '9105001MGR'],
        ];

        foreach ($managerUsers as $mgrUser) {
            $divisiKey = strtolower(trim($mgrUser['divisi']));
            if (!isset($divisiMap[$divisiKey])) {
                echo "Divisi tidak ditemukan: " . $mgrUser['divisi'] . PHP_EOL;
                continue;
            }
            
            // Check if user already exists
            $existingUser = DB::table('master_user')->where('username', $mgrUser['user'])->first();
            if ($existingUser) {
                echo "User sudah ada: " . $mgrUser['user'] . PHP_EOL;
                continue;
            }
            
            if (!$managerAkses) {
                continue;
            }
            
            DB::table('master_user')->insert([
                'nama' => $mgrUser['nama'],
                'username' => $mgrUser['user'],
                'nip' => $mgrUser['nip'],
                'password' => Hash::make('PCNJAYA123'),
                'master_auditee_id' => $divisiMap[$divisiKey],
                'master_akses_user_id' => $managerAkses->id,
            ]);
        }

        // Tambahkan 5 user dengan akses Assistant Manager (masing-masing ke 5 divisi yang sama)
        $assistantManagerUsers = [
            ['divisi' => 'Divisi Operasi', 'nama' => 'Assistant Manager Operasi', 'user' => 'ASMAN_OPS_01', 'nip' => '9201001ASM'],
            ['divisi' => 'Divisi Renus', 'nama' => 'Assistant Manager Renus', 'user' => 'ASMAN_REN_01', 'nip' => '9202001ASM'],
            ['divisi' => 'SETPER', 'nama' => 'Assistant Manager SETPER', 'user' => 'ASMAN_SET_01', 'nip' => '9203001ASM'],
            ['divisi' => 'DIVISI Keuangan', 'nama' => 'Assistant Manager Keuangan', 'user' => 'ASMAN_KEU_01', 'nip' => '9204001ASM'],
            ['divisi' => 'Divisi HC & Adm', 'nama' => 'Assistant Manager HC', 'user' => 'ASMAN_HC_01', 'nip' => '9205001ASM'],
        ];

        foreach ($assistantManagerUsers as $asmUser) {
            $divisiKey = strtolower(trim($asmUser['divisi']));
            if (!isset($divisiMap[$divisiKey])) {
                echo "Divisi tidak ditemukan: " . $asmUser['divisi'] . PHP_EOL;
                continue;
            }
            
            // Check if user already exists
            $existingUser = DB::table('master_user')->where('username', $asmUser['user'])->first();
            if ($existingUser) {
                echo "User sudah ada: " . $asmUser['user'] . PHP_EOL;
                continue;
            }
            
            if (!$assistantManagerAkses) {
                continue;
            }
            
            DB::table('master_user')->insert([
                'nama' => $asmUser['nama'],
                'username' => $asmUser['user'],
                'nip' => $asmUser['nip'],
                'password' => Hash::make('PCNJAYA123'),
                'master_auditee_id' => $divisiMap[$divisiKey],
                'master_akses_user_id' => $assistantManagerAkses->id,
            ]);
        }
    }
}