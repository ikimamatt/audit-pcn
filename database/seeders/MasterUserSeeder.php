<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MasterUserSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil mapping divisi ke id dari master_auditee (case-insensitive, trim)
        $divisiMap = DB::table('master_auditee')->get()->mapWithKeys(function($item) {
            return [strtolower(trim($item->divisi)) => $item->id];
        });

        // Ambil mapping akses user ke id
        $aksesMap = DB::table('master_akses_user')->get()->mapWithKeys(function($item) {
            return [strtoupper(trim($item->nama_akses)) => $item->id];
        });

        // Data dari gambar - User yang terlihat di tabel
        $usersFromImage = [
            // SPI Users
            [
                'nama' => 'DINAR AFIDAH PRAVITA PISPI',
                'username' => 'dinar.afidah',
                'nip' => '01253007PST',
                'divisi' => 'SPI',
                'akses' => 'AUDITOR',
                'jabatan' => 'JUNIOR OFFICER AUDITOR',
                'email' => 'dinar.afidah@example.com',
                'no_telpon' => '081234567890',
            ],
            [
                'nama' => 'AGIL FRASSETYO',
                'username' => 'agil.frassetyo',
                'nip' => '84091962',
                'divisi' => 'SPI',
                'akses' => 'KSPI',
                'jabatan' => 'KEPALA SATUAN PENGAWAS INTERNAL',
                'email' => 'agil.frassetyo@example.com',
                'no_telpon' => '081234567891',
            ],
            [
                'nama' => 'ASMAN SPI USER',
                'username' => 'asman.spi',
                'nip' => '85012345SPI',
                'divisi' => 'SPI',
                'akses' => 'ASMAN SPI',
                'jabatan' => 'ASISTEN MANAJER SPI',
                'email' => 'asman.spi@example.com',
                'no_telpon' => '081234567892',
            ],
            
            // BOD Users
            [
                'nama' => 'IRAWAN HERNANDA',
                'username' => 'irawan.hernanda',
                'nip' => '76020041CP',
                'divisi' => 'BOD',
                'akses' => 'VIEW BOD',
                'jabatan' => 'DIREKTUR UTAMA',
                'email' => 'irawan.hernanda@example.com',
                'no_telpon' => '081234567893',
            ],
            [
                'nama' => 'ANDRY APRIAWAN',
                'username' => 'andry.apriawan',
                'nip' => '7705003F',
                'divisi' => 'BOD',
                'akses' => 'VIEW BOD',
                'jabatan' => 'DIREKTUR OPERASI',
                'email' => 'andry.apriawan@example.com',
                'no_telpon' => '081234567894',
            ],
            [
                'nama' => 'FATAHUDDIN YOGI AMIBO',
                'username' => 'fatahuddin.yogi',
                'nip' => '7905004B',
                'divisi' => 'BOD',
                'akses' => 'VIEW BOD',
                'jabatan' => 'DIREKTUR OPERASI & PENGEMBANGAN USAHA',
                'email' => 'fatahuddin.yogi@example.com',
                'no_telpon' => '081234567895',
            ],
            
            // OPERASI Users
            [
                'nama' => 'FATAHUDDIN YOGI AMIBO',
                'username' => 'fatahuddin.yogi.ops',
                'nip' => '7905004B',
                'divisi' => 'OPERASI',
                'akses' => 'AUDITEE',
                'jabatan' => 'DIREKTUR OPERASI & PENGEMBANGAN USAHA',
                'email' => 'fatahuddin.yogi.ops@example.com',
                'no_telpon' => '081234567896',
            ],
            [
                'nama' => 'MANAGER OPERASI',
                'username' => 'manager.operasi',
                'nip' => '6724001PRO',
                'divisi' => 'OPERASI',
                'akses' => 'AUDITEE',
                'jabatan' => 'MANAGER OPERASI',
                'email' => 'manager.operasi@example.com',
                'no_telpon' => '081234567897',
            ],
            
            // RENUS IT Users
            [
                'nama' => 'FATAHUDDIN YOGI AMIBO',
                'username' => 'fatahuddin.yogi.renus',
                'nip' => '7905004B',
                'divisi' => 'RENUS IT',
                'akses' => 'AUDITEE',
                'jabatan' => 'DIREKTUR OPERASI & PENGEMBANGAN USAHA',
                'email' => 'fatahuddin.yogi.renus@example.com',
                'no_telpon' => '081234567898',
            ],
            [
                'nama' => 'MANAGER RENUS IT',
                'username' => 'manager.renus',
                'nip' => '8507283Z',
                'divisi' => 'RENUS IT',
                'akses' => 'AUDITEE',
                'jabatan' => 'MANAGER RENUS & IT',
                'email' => 'manager.renus@example.com',
                'no_telpon' => '081234567899',
            ],
            
            // SEKPER Users
            [
                'nama' => 'IRAWAN HERNANDA',
                'username' => 'irawan.hernanda.sekper',
                'nip' => '76020041CP',
                'divisi' => 'SEKPER',
                'akses' => 'AUDITEE',
                'jabatan' => 'DIREKTUR UTAMA',
                'email' => 'irawan.hernanda.sekper@example.com',
                'no_telpon' => '081234567900',
            ],
            [
                'nama' => 'SEKRETARIS PERUSAHAAN',
                'username' => 'sekretaris.perusahaan',
                'nip' => '7208028TRK',
                'divisi' => 'SEKPER',
                'akses' => 'AUDITEE',
                'jabatan' => 'SEKRETARIS PERUSAHAAN',
                'email' => 'sekretaris.perusahaan@example.com',
                'no_telpon' => '081234567901',
            ],
            
            // KEUANGAN Users
            [
                'nama' => 'SUPERVISOR AKUNTANSI',
                'username' => 'supervisor.akuntansi',
                'nip' => '8010035TRK',
                'divisi' => 'KEUANGAN',
                'akses' => 'AUDITEE',
                'jabatan' => 'SUPERVISOR AKUNTANSI',
                'email' => 'supervisor.akuntansi@example.com',
                'no_telpon' => '081234567902',
            ],
            [
                'nama' => 'MANAGER KEUANGAN',
                'username' => 'manager.keuangan',
                'nip' => '7510005TRK',
                'divisi' => 'KEUANGAN',
                'akses' => 'AUDITEE',
                'jabatan' => 'MANAGER KEUANGAN',
                'email' => 'manager.keuangan@example.com',
                'no_telpon' => '081234567903',
            ],
            
            // HUMAN CAPITAL Users
            [
                'nama' => 'MANAGER HUMAN CAPITAL',
                'username' => 'manager.hc',
                'nip' => '6924002PRO',
                'divisi' => 'HUMAN CAPITAL',
                'akses' => 'AUDITEE',
                'jabatan' => 'MANAGER HUMAN CAPITAL',
                'email' => 'manager.hc@example.com',
                'no_telpon' => '081234567904',
            ],
            
            // CABANG KALTIMRA Users
            [
                'nama' => 'MANAGER CABANG KALTIMRA',
                'username' => 'manager.kaltimra',
                'nip' => '8013085PWK',
                'divisi' => 'CABANG KALTIMRA',
                'akses' => 'AUDITEE',
                'jabatan' => 'MANAGER CABANG KALTIMRA',
                'email' => 'manager.kaltimra@example.com',
                'no_telpon' => '081234567905',
            ],
            
            // SUPER ADMIN
            [
                'nama' => 'IRVAN SANJAYA',
                'username' => 'irvan.sanjaya',
                'nip' => '90000001ADM',
                'divisi' => 'SUPER ADMIN',
                'akses' => 'SUPER ADMIN',
                'jabatan' => 'SUPER ADMINISTRATOR',
                'email' => 'irvan.sanjaya@example.com',
                'no_telpon' => '081234567906',
            ],
        ];

        // Data dummy untuk melengkapi (jika diperlukan)
        $dummyUsers = [
            // Auditor tambahan
            [
                'nama' => 'AUDITOR 2',
                'username' => 'auditor.2',
                'nip' => '01253008PST',
                'divisi' => 'SPI',
                'akses' => 'AUDITOR',
                'jabatan' => 'SENIOR OFFICER AUDITOR',
                'email' => 'auditor.2@example.com',
                'no_telpon' => '081234567907',
            ],
            [
                'nama' => 'AUDITOR 3',
                'username' => 'auditor.3',
                'nip' => '01253009PST',
                'divisi' => 'SPI',
                'akses' => 'AUDITOR',
                'jabatan' => 'OFFICER AUDITOR',
                'email' => 'auditor.3@example.com',
                'no_telpon' => '081234567908',
            ],
            
            // ASMAN SPI tambahan
            [
                'nama' => 'ASMAN SPI 2',
                'username' => 'asman.spi.2',
                'nip' => '85012346SPI',
                'divisi' => 'SPI',
                'akses' => 'ASMAN SPI',
                'jabatan' => 'ASISTEN MANAJER SPI',
                'email' => 'asman.spi.2@example.com',
                'no_telpon' => '081234567909',
            ],
            
            // Auditee dari berbagai divisi
            [
                'nama' => 'PIC AUDITEE OPERASI',
                'username' => 'pic.auditee.ops',
                'nip' => '9001001OPS',
                'divisi' => 'OPERASI',
                'akses' => 'AUDITEE',
                'jabatan' => 'PIC AUDITEE OPERASI',
                'email' => 'pic.auditee.ops@example.com',
                'no_telpon' => '081234567910',
            ],
            [
                'nama' => 'PIC AUDITEE RENUS',
                'username' => 'pic.auditee.renus',
                'nip' => '9002001REN',
                'divisi' => 'RENUS IT',
                'akses' => 'AUDITEE',
                'jabatan' => 'PIC AUDITEE RENUS',
                'email' => 'pic.auditee.renus@example.com',
                'no_telpon' => '081234567911',
            ],
            [
                'nama' => 'PIC AUDITEE KEUANGAN',
                'username' => 'pic.auditee.keu',
                'nip' => '9004001KEU',
                'divisi' => 'KEUANGAN',
                'akses' => 'AUDITEE',
                'jabatan' => 'PIC AUDITEE KEUANGAN',
                'email' => 'pic.auditee.keu@example.com',
                'no_telpon' => '081234567912',
            ],
            [
                'nama' => 'PIC AUDITEE HC',
                'username' => 'pic.auditee.hc',
                'nip' => '9005001HC',
                'divisi' => 'HUMAN CAPITAL',
                'akses' => 'AUDITEE',
                'jabatan' => 'PIC AUDITEE HUMAN CAPITAL',
                'email' => 'pic.auditee.hc@example.com',
                'no_telpon' => '081234567913',
            ],
            [
                'nama' => 'PIC AUDITEE SETPER',
                'username' => 'pic.auditee.setper',
                'nip' => '9003001SET',
                'divisi' => 'SEKPER',
                'akses' => 'AUDITEE',
                'jabatan' => 'PIC AUDITEE SETPER',
                'email' => 'pic.auditee.setper@example.com',
                'no_telpon' => '081234567914',
            ],
            
            // VIEW BOD tambahan
            [
                'nama' => 'DIREKTUR KEUANGAN',
                'username' => 'direktur.keuangan',
                'nip' => '7905005B',
                'divisi' => 'BOD',
                'akses' => 'VIEW BOD',
                'jabatan' => 'DIREKTUR KEUANGAN',
                'email' => 'direktur.keuangan@example.com',
                'no_telpon' => '081234567915',
            ],
        ];

        // Gabungkan semua user
        $allUsers = array_merge($usersFromImage, $dummyUsers);

        // Insert users
        foreach ($allUsers as $user) {
            $divisiKey = strtolower(trim($user['divisi']));
            $aksesKey = strtoupper(trim($user['akses']));
            
            if (!isset($divisiMap[$divisiKey])) {
                echo "Divisi tidak ditemukan: " . $user['divisi'] . PHP_EOL;
                continue;
            }
            
            if (!isset($aksesMap[$aksesKey])) {
                echo "Akses tidak ditemukan: " . $user['akses'] . PHP_EOL;
                continue;
            }
            
            // Check if user already exists
            $existingUser = DB::table('master_user')->where('username', $user['username'])->first();
            if ($existingUser) {
                echo "User sudah ada: " . $user['username'] . PHP_EOL;
                continue;
            }
            
            DB::table('master_user')->insert([
                'nama' => $user['nama'],
                'username' => $user['username'],
                'nip' => $user['nip'],
                'password' => Hash::make('PCNJAYA123'),
                'email' => $user['email'] ?? null,
                'no_telpon' => $user['no_telpon'] ?? null,
                'jabatan' => $user['jabatan'] ?? null,
                'master_auditee_id' => $divisiMap[$divisiKey],
                'master_akses_user_id' => $aksesMap[$aksesKey],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        echo "Seeder MasterUser berhasil dijalankan!" . PHP_EOL;
    }
}
