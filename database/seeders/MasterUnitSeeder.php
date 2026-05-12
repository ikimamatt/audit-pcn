<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterUnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['kode_unit' => 'U001', 'nama_unit' => 'Unit Pengawasan Internal'],
            ['kode_unit' => 'U002', 'nama_unit' => 'Unit Keuangan & Akuntansi'],
            ['kode_unit' => 'U003', 'nama_unit' => 'Unit Sumber Daya Manusia'],
            ['kode_unit' => 'U004', 'nama_unit' => 'Unit Teknologi Informasi'],
            ['kode_unit' => 'U005', 'nama_unit' => 'Unit Operasional & Produksi'],
            ['kode_unit' => 'U006', 'nama_unit' => 'Unit Pemasaran & Penjualan'],
            ['kode_unit' => 'U007', 'nama_unit' => 'Unit Kepatuhan & Regulasi'],
            ['kode_unit' => 'U008', 'nama_unit' => 'Unit Manajemen Risiko'],
            ['kode_unit' => 'U009', 'nama_unit' => 'Unit Pengadaan & Logistik'],
            ['kode_unit' => 'U010', 'nama_unit' => 'Unit Hukum & Sekretaris Perusahaan'],
        ];

        foreach ($units as &$unit) {
            $unit['created_at'] = now();
            $unit['updated_at'] = now();
        }

        DB::table('master_unit')->insert($units);

        $this->command->info('MasterUnitSeeder: ' . count($units) . ' data unit berhasil ditambahkan.');
    }
}
