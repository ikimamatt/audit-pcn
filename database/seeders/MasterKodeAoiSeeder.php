<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterKodeAoiSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('master_kode_aoi')->insert([
            [
                'indikator_pengawasan' => 'KEPATUHAN',
                'kode_area_of_improvement' => '01.01',
                'deskripsi_area_of_improvement' => 'Pelanggaran terhadap peraturan perundang-undangan yang berlaku',
            ],
            [
                'indikator_pengawasan' => 'KEPATUHAN',
                'kode_area_of_improvement' => '01.02',
                'deskripsi_area_of_improvement' => 'Pelanggaran terhadap prosedur dan tata kerja yang ditetapkan',
            ],
            [
                'indikator_pengawasan' => 'KEPATUHAN',
                'kode_area_of_improvement' => '01.03',
                'deskripsi_area_of_improvement' => 'Penyimpangan dari ketentuan pelaksanaan anggaran',
            ],
            [
                'indikator_pengawasan' => 'KEANDALAN & KEAKURATAN INFORMASI / LAPORAN',
                'kode_area_of_improvement' => '02.01',
                'deskripsi_area_of_improvement' => 'Keandalan & keakuratan administrasi, informasi / laporan keuangan dan non keuangan',
            ],
            [
                'indikator_pengawasan' => 'KEANDALAN & KEAKURATAN INFORMASI / LAPORAN',
                'kode_area_of_improvement' => '02.02',
                'deskripsi_area_of_improvement' => 'Keandalan & keakuratan informasi / laporan tata usaha langganan',
            ],
            [
                'indikator_pengawasan' => 'PENGAMANAN ASSET',
                'kode_area_of_improvement' => '03.01',
                'deskripsi_area_of_improvement' => 'Pengamanan Asset',
            ],
            [
                'indikator_pengawasan' => 'PEMANFAATAN SUMBER DAYA YANG EKONOMIS EFEKTIF DAN EFISIEN',
                'kode_area_of_improvement' => '04.01',
                'deskripsi_area_of_improvement' => 'Pemanfaatan sumber daya manusia',
            ],
            [
                'indikator_pengawasan' => 'PEMANFAATAN SUMBER DAYA YANG EKONOMIS EFEKTIF DAN EFISIEN',
                'kode_area_of_improvement' => '04.02',
                'deskripsi_area_of_improvement' => 'Pemanfaatan sumber daya material dan peralatan',
            ],
            [
                'indikator_pengawasan' => 'PEMANFAATAN SUMBER DAYA YANG EKONOMIS EFEKTIF DAN EFISIEN',
                'kode_area_of_improvement' => '04.03',
                'deskripsi_area_of_improvement' => 'Pemanfaatan sumber daya uang',
            ],
            [
                'indikator_pengawasan' => 'PENCAPAIAN TUJUAN SASARAN PROGRAM ATAU OPERASI',
                'kode_area_of_improvement' => '05.01',
                'deskripsi_area_of_improvement' => 'Pencapaian tujuan dan sasaran program atau operasi',
            ],
            [
                'indikator_pengawasan' => 'KASUS YANG MERUGIKAN PERUSAHAAN ATAU NEGARA',
                'kode_area_of_improvement' => '05.02',
                'deskripsi_area_of_improvement' => 'Kasus yang merugikan negara dan atau perusahaan',
            ],
            [
                'indikator_pengawasan' => 'KASUS YANG MERUGIKAN PERUSAHAAN ATAU NEGARA',
                'kode_area_of_improvement' => '05.03',
                'deskripsi_area_of_improvement' => 'Kewajiban penyetoran kepada negara dan atau perusahaan',
            ],
            [
                'indikator_pengawasan' => 'TEMUAN BERULANG',
                'kode_area_of_improvement' => '07.01',
                'deskripsi_area_of_improvement' => 'Temuan berulang terkait Kepatuhan',
            ],
            [
                'indikator_pengawasan' => 'TEMUAN BERULANG',
                'kode_area_of_improvement' => '07.02',
                'deskripsi_area_of_improvement' => 'Temuan berulang terkait Keandalan & Keakuratan Informasi/Laporan',
            ],
            [
                'indikator_pengawasan' => 'TEMUAN BERULANG',
                'kode_area_of_improvement' => '07.03',
                'deskripsi_area_of_improvement' => 'Temuan berulang terkait Pengamanan Asset',
            ],
            [
                'indikator_pengawasan' => 'TEMUAN BERULANG',
                'kode_area_of_improvement' => '07.04',
                'deskripsi_area_of_improvement' => 'Temuan berulang terkait Pemanfaatan Sumber Daya yang Ekonomis Efektif dan Efisien',
            ],
            [
                'indikator_pengawasan' => 'TEMUAN BERULANG',
                'kode_area_of_improvement' => '07.05',
                'deskripsi_area_of_improvement' => 'Temuan berulang terkait Pencapaian Tujuan Sasaran Program atau Operasi',
            ],
            [
                'indikator_pengawasan' => 'TEMUAN BERULANG',
                'kode_area_of_improvement' => '07.06',
                'deskripsi_area_of_improvement' => 'Temuan berulang terkait Kasus yang Merugikan Perusahaan atau Negara',
            ],
        ]);
    }
} 