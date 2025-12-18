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
            // ========== SPI Users ==========
            [
                'nama' => 'DINAR AFIDAH PRAVITA PUTRI',
                'username' => 'dinar.afidah',
                'nip' => '01253007PST',
                'divisi' => 'SPI',
                'akses' => 'AUDITOR',
                'jabatan' => 'JUNIOR OFFICER AUDITOR',
                'email' => 'dinar.afidah@pcn.co.id',
                'no_telpon' => '081234567001',
            ],
            [
                'nama' => 'ASMAN SPI',
                'username' => 'asman.spi',
                'nip' => '85012345SPI',
                'divisi' => 'SPI',
                'akses' => 'ASMAN SPI',
                'jabatan' => 'ASISTEN MANAGER SPI',
                'email' => 'asman.spi@pcn.co.id',
                'no_telpon' => '081234567002',
            ],
            [
                'nama' => 'AGIL FRASSETYO',
                'username' => 'agil.frassetyo',
                'nip' => '84091962',
                'divisi' => 'SPI',
                'akses' => 'KSPI',
                'jabatan' => 'KEPALA SATUAN PENGAWAS INTERNAL',
                'email' => 'agil.frassetyo@pcn.co.id',
                'no_telpon' => '081234567003',
            ],
            
            // ========== KEUANGAN Users ==========
            [
                'nama' => 'DEWI SATYA NINGSIH',
                'username' => 'dewi.satya',
                'nip' => '8010035TRK',
                'divisi' => 'KEUANGAN',
                'akses' => 'AUDITEE',
                'jabatan' => 'SUPERVISOR AKUNTANSI',
                'email' => 'dewi.satya@pcn.co.id',
                'no_telpon' => '081234567004',
            ],
            [
                'nama' => 'ANDI RIPANSYAH',
                'username' => 'andi.ripansyah',
                'nip' => '7610036TRK',
                'divisi' => 'KEUANGAN',
                'akses' => 'AUDITEE',
                'jabatan' => 'ASMAN KEUANGAN & ANGGARAN',
                'email' => 'andi.ripansyah@pcn.co.id',
                'no_telpon' => '081234567005',
            ],
            [
                'nama' => 'YUSUF SAEFUDIN',
                'username' => 'yusuf.saefudin',
                'nip' => '7510005TRK',
                'divisi' => 'KEUANGAN',
                'akses' => 'AUDITEE',
                'jabatan' => 'MANAGER KEUANGAN',
                'email' => 'yusuf.saefudin@pcn.co.id',
                'no_telpon' => '081234567006',
            ],
            
            // ========== RENUS IT Users ==========
            [
                'nama' => 'BUDI MULYONO',
                'username' => 'budi.mulyono',
                'nip' => '8207283REN',
                'divisi' => 'RENUS IT',
                'akses' => 'AUDITEE',
                'jabatan' => 'ASMAN PERENCANAAN DAN PENGEMBANGAN USAHA',
                'email' => 'budi.mulyono@pcn.co.id',
                'no_telpon' => '081234567007',
            ],
            [
                'nama' => 'RIZKA ABDULLAH',
                'username' => 'rizka.abdullah',
                'nip' => '8507284REN',
                'divisi' => 'RENUS IT',
                'akses' => 'AUDITEE',
                'jabatan' => 'MANAGER PERENCANAAN DAN PENGEMBANGAN USAHA',
                'email' => 'rizka.abdullah@pcn.co.id',
                'no_telpon' => '081234567008',
            ],
            [
                'nama' => 'FATAHUDDIN YOGI AMIBOWO',
                'username' => 'fatahuddin.yogi.renus',
                'nip' => '7905004BREN',
                'divisi' => 'RENUS IT',
                'akses' => 'AUDITEE',
                'jabatan' => 'DIREKTUR OPERASI & PENGEMBANGAN USAHA',
                'email' => 'fatahuddin.yogi.renus@pcn.co.id',
                'no_telpon' => '081234567009',
            ],
            
            // ========== OPERASI Users ==========
            [
                'nama' => 'WAHYU KURNIAWAN',
                'username' => 'wahyu.kurniawan',
                'nip' => '6724001OPS',
                'divisi' => 'OPERASI',
                'akses' => 'AUDITEE',
                'jabatan' => 'SUPERVISOR LOGISTIK',
                'email' => 'wahyu.kurniawan@pcn.co.id',
                'no_telpon' => '081234567010',
            ],
            [
                'nama' => 'ROESMIN',
                'username' => 'roesmin',
                'nip' => '6824002OPS',
                'divisi' => 'OPERASI',
                'akses' => 'AUDITEE',
                'jabatan' => 'ASMAN OPHARDUNG',
                'email' => 'roesmin@pcn.co.id',
                'no_telpon' => '081234567011',
            ],
            [
                'nama' => 'FATAHUDDIN YOGI AMIBOWO',
                'username' => 'fatahuddin.yogi.ops',
                'nip' => '7905004BOPS',
                'divisi' => 'OPERASI',
                'akses' => 'AUDITEE',
                'jabatan' => 'DIREKTUR OPERASI & PENGEMBANGAN USAHA',
                'email' => 'fatahuddin.yogi.ops@pcn.co.id',
                'no_telpon' => '081234567012',
            ],
            
            // ========== HUMAN CAPITAL Users ==========
            [
                'nama' => 'PRASETIO NINGSIH',
                'username' => 'prasetio.ningsih',
                'nip' => '6924001HC',
                'divisi' => 'HUMAN CAPITAL',
                'akses' => 'AUDITEE',
                'jabatan' => 'SPV. PELAYANAN HUMAN CAPITAL',
                'email' => 'prasetio.ningsih@pcn.co.id',
                'no_telpon' => '081234567013',
            ],
            [
                'nama' => 'EMAN SLAMET WIDODO',
                'username' => 'eman.slamet',
                'nip' => '6924002HC',
                'divisi' => 'HUMAN CAPITAL',
                'akses' => 'AUDITEE',
                'jabatan' => 'ASMAN HUMAN CAPITAL',
                'email' => 'eman.slamet@pcn.co.id',
                'no_telpon' => '081234567014',
            ],
            [
                'nama' => 'YAINUS SHOLEH',
                'username' => 'yainus.sholeh',
                'nip' => '6924003HC',
                'divisi' => 'HUMAN CAPITAL',
                'akses' => 'AUDITEE',
                'jabatan' => 'MANAGER HUMAN CAPITAL DAN ADMINISTRASI UMUM',
                'email' => 'yainus.sholeh@pcn.co.id',
                'no_telpon' => '081234567015',
            ],
            
            // ========== SEKPER Users ==========
            [
                'nama' => 'NURUL AZISAH',
                'username' => 'nurul.azisah',
                'nip' => '7208027SEK',
                'divisi' => 'SEKPER',
                'akses' => 'AUDITEE',
                'jabatan' => 'JUNIOR OFFICER KOMUNIKASI DAN TATA KELOLA',
                'email' => 'nurul.azisah@pcn.co.id',
                'no_telpon' => '081234567016',
            ],
            [
                'nama' => 'ROMY HARYADI',
                'username' => 'romy.haryadi',
                'nip' => '7208028SEK',
                'divisi' => 'SEKPER',
                'akses' => 'AUDITEE',
                'jabatan' => 'ASMAN HUKUM DAN TATA KELOLA',
                'email' => 'romy.haryadi@pcn.co.id',
                'no_telpon' => '081234567017',
            ],
            [
                'nama' => 'IRAWAN HERNANDA',
                'username' => 'irawan.hernanda.sekper',
                'nip' => '76020041SEK',
                'divisi' => 'SEKPER',
                'akses' => 'AUDITEE',
                'jabatan' => 'DIREKTUR UTAMA',
                'email' => 'irawan.hernanda.sekper@pcn.co.id',
                'no_telpon' => '081234567018',
            ],
            
            // ========== BOD Users ==========
            [
                'nama' => 'IRAWAN HERNANDA',
                'username' => 'irawan.hernanda',
                'nip' => '76020041BOD',
                'divisi' => 'BOD',
                'akses' => 'VIEW BOD',
                'jabatan' => 'DIREKTUR UTAMA',
                'email' => 'irawan.hernanda@pcn.co.id',
                'no_telpon' => '081234567019',
            ],
            [
                'nama' => 'ANDRY APRIAWAN',
                'username' => 'andry.apriawan',
                'nip' => '7705003BOD',
                'divisi' => 'BOD',
                'akses' => 'VIEW BOD',
                'jabatan' => 'DIREKTUR KEUANGAN DAN ADMINISTRASI',
                'email' => 'andry.apriawan@pcn.co.id',
                'no_telpon' => '081234567020',
            ],
            [
                'nama' => 'FATAHUDDIN YOGI AMIBOWO',
                'username' => 'fatahuddin.yogi',
                'nip' => '7905004BOD',
                'divisi' => 'BOD',
                'akses' => 'VIEW BOD',
                'jabatan' => 'DIREKTUR OPERASI & PENGEMBANGAN USAHA',
                'email' => 'fatahuddin.yogi@pcn.co.id',
                'no_telpon' => '081234567021',
            ],
            
            // ========== SUPER ADMIN ==========
            [
                'nama' => 'IRVAN SANJAYA',
                'username' => 'irvan.sanjaya',
                'nip' => '90000001ADM',
                'divisi' => 'SUPER ADMIN',
                'akses' => 'SUPER ADMIN',
                'jabatan' => 'ASMAN IT',
                'email' => 'irvan.sanjaya@pcn.co.id',
                'no_telpon' => '081234567022',
            ],
            
            // ========== CABANG KALTIMRA Users ==========
            [
                'nama' => 'OKTO INDRA LESMANA',
                'username' => 'okto.indra',
                'nip' => '8013084KAL',
                'divisi' => 'CABANG KALTIMRA',
                'akses' => 'AUDITEE',
                'jabatan' => 'JUNIOR OFFICER OPERASI CABANG/SITE',
                'email' => 'okto.indra@pcn.co.id',
                'no_telpon' => '081234567023',
            ],
            [
                'nama' => 'JOKO SUTRISNO',
                'username' => 'joko.sutrisno',
                'nip' => '8013085KAL',
                'divisi' => 'CABANG KALTIMRA',
                'akses' => 'AUDITEE',
                'jabatan' => 'SUPERVISOR OPERASI',
                'email' => 'joko.sutrisno@pcn.co.id',
                'no_telpon' => '081234567024',
            ],
            [
                'nama' => 'DONY BAYUMAR',
                'username' => 'dony.bayumar',
                'nip' => '8013086KAL',
                'divisi' => 'CABANG KALTIMRA',
                'akses' => 'AUDITEE',
                'jabatan' => 'MANAGER CABANG KALTIMRA',
                'email' => 'dony.bayumar@pcn.co.id',
                'no_telpon' => '081234567025',
            ],
        ];

        // Data dummy tambahan (untuk testing/development - opsional)
        $dummyUsers = [
            // Tambahan user untuk testing jika diperlukan
            // Bisa dikosongkan jika hanya ingin menggunakan data asli dari gambar
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
