<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PelaporanHasilAuditSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pelaporan_hasil_audit')->insert([
            [
                'nomor_urut' => 1,
                'tahun' => 2024,
                'perencanaan_audit_id' => 1,
                'nomor_lha_lhk' => '001.LHA/PO/SPI.01.02/SPI.PCN/2024',
                'jenis_lha_lhk' => 'LHA',
                'po_audit_konsul' => 'PO AUDIT',
                'kode_spi' => 'SPI.01.02',
                'status_approval' => 'approved',
                'approved_by' => 1,
                'approved_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nomor_urut' => 2,
                'tahun' => 2024,
                'perencanaan_audit_id' => 1,
                'nomor_lha_lhk' => '002.LHK/KONSUL/SPI.01.03/SPI.PCN/2024',
                'jenis_lha_lhk' => 'LHK',
                'po_audit_konsul' => 'KONSUL',
                'kode_spi' => 'SPI.01.03',
                'status_approval' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nomor_urut' => 3,
                'tahun' => 2024,
                'perencanaan_audit_id' => 1,
                'nomor_lha_lhk' => '003.LHA/PO/SPI.01.04/SPI.PCN/2024',
                'jenis_lha_lhk' => 'LHA',
                'po_audit_konsul' => 'PO AUDIT',
                'kode_spi' => 'SPI.01.04',
                'status_approval' => 'approved',
                'approved_by' => 1,
                'approved_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
} 