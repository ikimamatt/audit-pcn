<?php

namespace App\Services\Audit;

use App\Models\Audit\PerencanaanAudit;
use App\Models\Models\Audit\PelaporanHasilAudit;
use App\Models\Audit\PelaporanTemuan;
use App\Models\MasterData\MasterJenisAudit;
use App\Models\MasterData\MasterKodeAoi;
use App\Models\MasterData\MasterKodeRisk;
use Illuminate\Support\Facades\DB;

class NomorGeneratorService
{
    /**
     * Generate automatic nomor surat tugas.
     * Format: 001.STG/SPI.01.XX/SPI-PCN/YYYY
     *
     * @param string|null $jenisAudit
     * @return string
     */
    public function generateNomorSuratTugas(?string $jenisAudit = null): string
    {
        return DB::transaction(function () use ($jenisAudit) {
            $tahun = date('Y');
            $kodeJenis = '02'; // Default untuk audit operasional
            
            if ($jenisAudit) {
                switch (strtolower($jenisAudit)) {
                    case 'audit operasional':
                        $kodeJenis = '02';
                        break;
                    case 'audit khusus':
                        $kodeJenis = '03';
                        break;
                    case 'konsultasi':
                        $kodeJenis = '04';
                        break;
                    default:
                        $kodeJenis = '02';
                        break;
                }
            }
            
            $lastNomor = PerencanaanAudit::where('jenis_audit', $jenisAudit)
                ->whereYear('created_at', $tahun)
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();
            
            if ($lastNomor) {
                $nomorParts = explode('.', $lastNomor->nomor_surat_tugas);
                $nomorUrut = intval($nomorParts[0]) + 1;
            } else {
                $nomorUrut = 1;
            }
            
            $nomorFormatted = str_pad($nomorUrut, 3, '0', STR_PAD_LEFT);
            
            return "{$nomorFormatted}.STG/SPI.01.{$kodeJenis}/SPI-PCN/{$tahun}";
        });
    }

    /**
     * Generate automatic nomor LHA/LHK.
     * Format: XXX/AA/BB/CC/SPI.PCN.YYYY
     *
     * @param string $jenisLhaLhk
     * @param int $jenisAuditId
     * @param string $kodeSpi
     * @return array
     */
    public function generateNomorLhaLhk(string $jenisLhaLhk, string $jenisAuditId, string $kodeSpi): array
    {
        return DB::transaction(function () use ($jenisLhaLhk, $jenisAuditId, $kodeSpi) {
            $jenisAudit = MasterJenisAudit::findOrFail($jenisAuditId);
            $poKonsul = ($jenisAudit->kode == 'SPI.01.04') ? 'KONSUL' : 'POAUDIT';

            $currentYear = date('Y');
            $lastLhaLhk = PelaporanHasilAudit::where('tahun', $currentYear)
                ->lockForUpdate()
                ->orderBy('nomor_urut', 'desc')
                ->first();
            
            $nextNumber = $lastLhaLhk ? ($lastLhaLhk->nomor_urut + 1) : 1;
            $nomorLhaLhk = sprintf('%03d', $nextNumber) . '/' . $jenisLhaLhk . '/' . $poKonsul . '/' . $kodeSpi . '/SPI.PCN.' . $currentYear;
            
            return [
                'nomor_lha_lhk' => $nomorLhaLhk,
                'nomor_urut' => $nextNumber
            ];
        });
    }

    /**
     * Generate automatic nomor ISS.
     * Format: ISS.XXX/PO PCN/MM/NN/PP/YYYY
     * Nomor urut ISS di-scope per surat tugas (perencanaan_audit_id).
     *
     * @param string $kodeAoiId
     * @param string $kodeRiskId
     * @param string $kodeSpi
     * @param string|null $perencanaanAuditId
     * @param int|null $existingNomorUrut  Jika diisi (edit ISS lama), gunakan nomor ini tanpa auto-increment
     * @return array
     */
    public function generateNomorIss(string $kodeAoiId, string $kodeRiskId, string $kodeSpi = 'SPI.01.02', ?string $perencanaanAuditId = null, ?int $existingNomorUrut = null): array
    {
        return DB::transaction(function () use ($kodeAoiId, $kodeRiskId, $kodeSpi, $perencanaanAuditId, $existingNomorUrut) {
            $kodeAoi = MasterKodeAoi::findOrFail($kodeAoiId);
            $kodeRisk = MasterKodeRisk::findOrFail($kodeRiskId);
            
            $currentYear = date('Y');

            // Jika ISS lama sedang diedit, gunakan nomor urut yang sudah ada
            if ($existingNomorUrut) {
                $nextIssNumber = $existingNomorUrut;
            } else {
                // ISS baru: cari nomor urut tertinggi di scope surat tugas ini
                $query = PelaporanTemuan::where('pelaporan_temuan.tahun', $currentYear);

                if ($perencanaanAuditId) {
                    $query->join('pelaporan_hasil_audit', 'pelaporan_temuan.pelaporan_hasil_audit_id', '=', 'pelaporan_hasil_audit.id')
                          ->where('pelaporan_hasil_audit.perencanaan_audit_id', $perencanaanAuditId);
                }

                $lastIss = $query->lockForUpdate()
                    ->orderBy('pelaporan_temuan.nomor_urut_iss', 'desc')
                    ->select('pelaporan_temuan.*')
                    ->first();
                
                $nextIssNumber = $lastIss ? ($lastIss->nomor_urut_iss + 1) : 1;
            }

            $nomorIss = 'ISS.' . sprintf('%03d', $nextIssNumber) . '/PO PCN/' . $kodeSpi . '/' . $kodeAoi->kode_area_of_improvement . '/' . $kodeRisk->kode_risiko . '/' . $currentYear;
            
            return [
                'nomor_iss' => $nomorIss,
                'nomor_urut_iss' => $nextIssNumber
            ];
        });
    }
}
