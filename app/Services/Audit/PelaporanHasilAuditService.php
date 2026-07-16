<?php

namespace App\Services\Audit;

use App\Models\Models\Audit\PelaporanHasilAudit;
use App\Models\Audit\PelaporanTemuan;
use Illuminate\Support\Facades\DB;

class PelaporanHasilAuditService
{
    /**
     * Create a new Pelaporan Hasil Audit with its Temuan (ISS) records.
     *
     * @param array $data
     * @return PelaporanHasilAudit
     */
    public function create(array $data): PelaporanHasilAudit
    {
        return DB::transaction(function () use ($data) {
            $nomorParts = explode('/', $data['nomor_lha_lhk']);
            $nomorUrut = intval($nomorParts[0]);

            $pelaporan = PelaporanHasilAudit::create([
                'perencanaan_audit_id' => $data['perencanaan_audit_id'],
                'nomor_lha_lhk'        => $data['nomor_lha_lhk'],
                'jenis_lha_lhk'        => $data['jenis_lha_lhk'],
                'kode_spi'             => $data['kode_spi'],
                'jenis_audit_id'       => $data['jenis_audit_id'],
                'nomor_urut'           => $nomorUrut,
                'tahun'                => date('Y'),
                'status_approval'      => 'pending',
            ]);

            // Calculate starting offset of ISS numbering for this perencanaan_audit_id
            $startOffset = PelaporanTemuan::join('pelaporan_hasil_audit', 'pelaporan_temuan.pelaporan_hasil_audit_id', '=', 'pelaporan_hasil_audit.id')
                ->where('pelaporan_hasil_audit.perencanaan_audit_id', $data['perencanaan_audit_id'])
                ->where('pelaporan_temuan.tahun', date('Y'))
                ->max('pelaporan_temuan.nomor_urut_iss') ?? 0;

            foreach ($data['hasil_temuan'] as $index => $hasilTemuan) {
                $nextIssNumber = $startOffset + $index + 1; // Order sequentially starting from offset + 1
                $kodeAoi = \App\Models\MasterData\MasterKodeAoi::findOrFail($data['kode_aoi_id'][$index]);
                $kodeRisk = \App\Models\MasterData\MasterKodeRisk::findOrFail($data['kode_risk_id'][$index]);
                $currentYear = date('Y');
                $nomorIss = 'ISS.' . sprintf('%03d', $nextIssNumber) . '/PO PCN/' . $pelaporan->kode_spi . '/' . $kodeAoi->kode_area_of_improvement . '/' . $kodeRisk->kode_risiko . '/' . $currentYear;

                PelaporanTemuan::create([
                    'pelaporan_hasil_audit_id' => $pelaporan->id,
                    'hasil_temuan'             => $hasilTemuan,
                    'kode_aoi_id'              => $data['kode_aoi_id'][$index],
                    'kode_risk_id'             => $data['kode_risk_id'][$index],
                    'nomor_iss'                => $nomorIss,
                    'nomor_urut_iss'           => $nextIssNumber,
                    'tahun'                    => date('Y'),
                    'permasalahan'             => $data['permasalahan'][$index],
                    'penyebab'                 => $data['penyebab'][$index],
                    'kriteria'                 => $data['kriteria'][$index],
                    'dampak_terjadi'           => $data['dampak_terjadi'][$index] ?? null,
                    'dampak_potensi'           => $data['dampak_potensi'][$index] ?? null,
                    'signifikan'               => $data['signifikan'][$index],
                    'status_approval'          => 'pending',
                ]);
            }

            return $pelaporan;
        });
    }

    /**
     * Update an existing Pelaporan Hasil Audit and sync its Temuan records.
     *
     * @param PelaporanHasilAudit $item
     * @param array $data
     * @return PelaporanHasilAudit
     */
    public function update(PelaporanHasilAudit $item, array $data): PelaporanHasilAudit
    {
        return DB::transaction(function () use ($item, $data) {
            $item->update([
                'perencanaan_audit_id' => $data['perencanaan_audit_id'],
                'nomor_lha_lhk'        => $data['nomor_lha_lhk'],
                'jenis_lha_lhk'        => $data['jenis_lha_lhk'],
                'jenis_audit_id'       => $data['jenis_audit_id'],
                'kode_spi'             => $data['kode_spi'],
                'status_approval'      => 'pending',
                'approved_by'          => null,
                'approved_at'          => null,
                'approved_by_level1'   => null,
                'approved_at_level1'   => null,
                'alasan_reject'        => null,
            ]);

            $submittedIds = array_filter($data['temuan_id'] ?? []);

            if (!empty($submittedIds)) {
                PelaporanTemuan::where('pelaporan_hasil_audit_id', $item->id)
                    ->whereNotIn('id', $submittedIds)
                    ->delete();
            } else {
                PelaporanTemuan::where('pelaporan_hasil_audit_id', $item->id)->delete();
            }

            // Calculate starting offset of ISS numbering for this perencanaan_audit_id (excluding current LHA)
            $startOffset = PelaporanTemuan::join('pelaporan_hasil_audit', 'pelaporan_temuan.pelaporan_hasil_audit_id', '=', 'pelaporan_hasil_audit.id')
                ->where('pelaporan_hasil_audit.perencanaan_audit_id', $data['perencanaan_audit_id'])
                ->where('pelaporan_hasil_audit.id', '!=', $item->id)
                ->where('pelaporan_temuan.tahun', date('Y'))
                ->max('pelaporan_temuan.nomor_urut_iss') ?? 0;

            foreach ($data['hasil_temuan'] as $i => $hasilTemuan) {
                $temuanId = $data['temuan_id'][$i] ?? null;
                
                $nextIssNumber = $startOffset + $i + 1; // Order sequentially starting from offset + 1
                $kodeAoi = \App\Models\MasterData\MasterKodeAoi::findOrFail($data['kode_aoi_id'][$i]);
                $kodeRisk = \App\Models\MasterData\MasterKodeRisk::findOrFail($data['kode_risk_id'][$i]);
                $currentYear = date('Y');
                $nomorIss = 'ISS.' . sprintf('%03d', $nextIssNumber) . '/PO PCN/' . $item->kode_spi . '/' . $kodeAoi->kode_area_of_improvement . '/' . $kodeRisk->kode_risiko . '/' . $currentYear;

                $temuanData = [
                    'pelaporan_hasil_audit_id' => $item->id,
                    'hasil_temuan'             => $hasilTemuan,
                    'kode_aoi_id'              => $data['kode_aoi_id'][$i],
                    'kode_risk_id'             => $data['kode_risk_id'][$i],
                    'nomor_iss'                => $nomorIss,
                    'nomor_urut_iss'           => $nextIssNumber,
                    'tahun'                    => date('Y'),
                    'permasalahan'             => $data['permasalahan'][$i],
                    'penyebab'                 => $data['penyebab'][$i],
                    'kriteria'                 => $data['kriteria'][$i],
                    'dampak_terjadi'           => $data['dampak_terjadi'][$i] ?? null,
                    'dampak_potensi'           => $data['dampak_potensi'][$i] ?? null,
                    'signifikan'               => $data['signifikan'][$i],
                    'status_approval'          => 'pending',
                    'approved_by'              => null,
                    'approved_at'              => null,
                ];

                if ($temuanId) {
                    PelaporanTemuan::where('id', $temuanId)->update($temuanData);
                } else {
                    PelaporanTemuan::create($temuanData);
                }
            }

            return $item;
        });
    }

    /**
     * Delete a Pelaporan Hasil Audit.
     *
     * @param PelaporanHasilAudit $item
     * @return void
     */
    public function delete(PelaporanHasilAudit $item): void
    {
        DB::transaction(function () use ($item) {
            $item->delete(); // cascade deletes temuan/ISS records if set in DB or handled by model events
        });
    }

    /**
     * Store a standalone Temuan record.
     *
     * @param array $data
     * @return PelaporanTemuan
     */
    public function storeTemuan(array $data): PelaporanTemuan
    {
        return PelaporanTemuan::create($data);
    }

    /**
     * Update an existing standalone Temuan record.
     *
     * @param PelaporanTemuan $temuan
     * @param array $data
     * @return PelaporanTemuan
     */
    public function updateTemuan(PelaporanTemuan $temuan, array $data): PelaporanTemuan
    {
        $kodeAoi = \App\Models\MasterData\MasterKodeAoi::findOrFail($data['kode_aoi_id']);
        $kodeRisk = \App\Models\MasterData\MasterKodeRisk::findOrFail($data['kode_risk_id']);
        
        $pelaporan = $temuan->pelaporanHasilAudit;
        $kodeSpi = $pelaporan ? $pelaporan->kode_spi : 'SPI.01.02';
        $currentYear = date('Y');
        
        // Preserve the existing sequence number for modal inline edit
        $nextIssNumber = $temuan->nomor_urut_iss ?: 1;
        $nomorIss = 'ISS.' . sprintf('%03d', $nextIssNumber) . '/PO PCN/' . $kodeSpi . '/' . $kodeAoi->kode_area_of_improvement . '/' . $kodeRisk->kode_risiko . '/' . $currentYear;
        
        $data['nomor_iss'] = $nomorIss;

        $temuan->update(array_merge($data, [
            'status_approval' => 'pending',
            'approved_by'     => null,
            'approved_at'     => null,
        ]));

        // Reset parent LHA status to pending as well
        if ($pelaporan) {
            $pelaporan->update([
                'status_approval'    => 'pending',
                'approved_by'        => null,
                'approved_at'        => null,
                'approved_by_level1' => null,
                'approved_at_level1' => null,
                'alasan_reject'      => null,
            ]);
        }

        return $temuan;
    }

    /**
     * Delete a standalone Temuan record.
     *
     * @param PelaporanTemuan $temuan
     * @return void
     */
    public function deleteTemuan(PelaporanTemuan $temuan): void
    {
        $temuan->delete();
    }

    /**
     * Process approval/rejection for Pelaporan Hasil Audit.
     *
     * @param PelaporanHasilAudit $item
     * @param string $action
     * @param string|null $reason
     * @return array
     */
    public function approve(PelaporanHasilAudit $item, string $action, ?string $reason): array
    {
        return DB::transaction(function () use ($item, $action, $reason) {
            $result = \App\Helpers\ApprovalHelper::processApproval(
                $item,
                $action,
                $reason
            );

            if ($result['success']) {
                $item->refresh();
                
                // If final approval (approved), approve all associated findings (ISS)
                if ($action === 'approve' && $item->status_approval === 'approved') {
                    foreach ($item->temuan as $temuan) {
                        $temuan->update([
                            'status_approval' => 'approved',
                            'approved_by'     => auth()->id(),
                            'approved_at'     => now()
                        ]);
                    }
                    $result['message'] .= ' Semua ISS juga berhasil diapprove!';
                }
                
                // If final rejection (rejected), reject all associated findings (ISS)
                if ($action === 'reject' && $item->status_approval === 'rejected') {
                    foreach ($item->temuan as $temuan) {
                        $temuan->update([
                            'status_approval' => 'rejected',
                            'approved_by'     => auth()->id(),
                            'approved_at'     => now()
                        ]);
                    }
                    $result['message'] .= ' Semua ISS juga berhasil direject!';
                }
            }

            return $result;
        });
    }

    /**
     * Standalone approval/rejection for a Temuan (ISS) record.
     *
     * @param mixed $temuan
     * @param string $action
     * @param string|null $reason
     * @return array
     */
    public function approveTemuan($temuan, string $action, ?string $reason): array
    {
        $temuan->status_approval = $action === 'approve' ? 'approved' : 'rejected';
        $temuan->approved_by = auth()->id();
        $temuan->approved_at = now();
        if ($action === 'reject') {
            $temuan->alasan_reject = $reason;
        } else {
            $temuan->alasan_reject = null;
        }
        $temuan->save();

        return [
            'success' => true,
            'message' => $action === 'approve'
                ? 'Status temuan audit (ISS) berhasil disetujui!'
                : 'Status temuan audit (ISS) berhasil ditolak!'
        ];
    }
}
