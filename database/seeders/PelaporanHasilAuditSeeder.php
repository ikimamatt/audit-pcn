<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PelaporanHasilAuditSeeder extends Seeder
{
    public function run(): void
    {
        $perencanaanIds = DB::table('perencanaan_audit')->pluck('id')->toArray();
        $userIds = DB::table('master_user')->pluck('id')->toArray();
        $jenisAuditIds = DB::table('master_jenis_audit')->pluck('id')->toArray();

        $insertData = [
            [
                'nomor_urut' => 1,
                'tahun' => 2024,
                'perencanaan_audit_id' => $perencanaanIds[0] ?? null,
                'nomor_lha_lhk' => '001.LHA/PO/SPI.01.02/SPI.PCN/2024',
                'jenis_lha_lhk' => 'LHA',
                'kode_spi' => 'SPI.01.02',
                'jenis_audit_id' => null, // Bisa diisi dengan ID dari master_jenis_audit jika ada
                'status_approval' => 'approved',
                'approved_by' => $userIds[0] ?? null,
                'approved_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nomor_urut' => 2,
                'tahun' => 2024,
                'perencanaan_audit_id' => $perencanaanIds[0] ?? null,
                'nomor_lha_lhk' => '002.LHK/KONSUL/SPI.01.03/SPI.PCN/2024',
                'jenis_lha_lhk' => 'LHK',
                'kode_spi' => 'SPI.01.03',
                'jenis_audit_id' => null, // Bisa diisi dengan ID dari master_jenis_audit jika ada
                'status_approval' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nomor_urut' => 3,
                'tahun' => 2024,
                'perencanaan_audit_id' => $perencanaanIds[0] ?? null,
                'nomor_lha_lhk' => '003.LHA/PO/SPI.01.04/SPI.PCN/2024',
                'jenis_lha_lhk' => 'LHA',
                'kode_spi' => 'SPI.01.04',
                'jenis_audit_id' => null, // Bisa diisi dengan ID dari master_jenis_audit jika ada
                'status_approval' => 'approved',
                'approved_by' => $userIds[0] ?? null,
                'approved_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
        foreach ($insertData as &$row) {
            $row['id'] = (string) \Illuminate\Support\Str::uuid();
        }
        DB::table('pelaporan_hasil_audit')->insert($insertData);
    }
} 