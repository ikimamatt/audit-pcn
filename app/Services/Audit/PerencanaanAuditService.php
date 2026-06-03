<?php

namespace App\Services\Audit;

use App\Models\Audit\PerencanaanAudit;
use App\Models\MasterData\MasterUser;
use App\Models\MasterData\MasterJenisAudit;
use App\Models\Models\Audit\JadwalPkptAudit;

class PerencanaanAuditService
{
    /**
     * Create a new Perencanaan Audit.
     *
     * @param array $data
     * @return PerencanaanAudit
     */
    public function create(array $data): PerencanaanAudit
    {
        $auditorData = $this->formatAuditors($data['auditor'] ?? []);
        $jenisAudit = MasterJenisAudit::find($data['jenis_audit_id']);

        return PerencanaanAudit::create([
            'tanggal_surat_tugas'  => $data['tanggal_surat_tugas'],
            'nomor_surat_tugas'    => $data['nomor_surat_tugas'],
            'jenis_audit_id'       => $data['jenis_audit_id'],
            'jenis_audit'          => $jenisAudit ? $jenisAudit->nama_jenis_audit : null,
            'auditor'              => $auditorData,
            'auditee_id'           => $data['auditee'],
            'area_id'              => $data['area_id'] ?? null,
            'ruang_lingkup'        => $data['ruang_lingkup'],
            'tanggal_audit_mulai'  => $data['tanggal_audit_mulai'],
            'tanggal_audit_sampai' => $data['tanggal_audit_sampai'],
            'periode_audit'        => $data['periode_awal'] . ' s/d ' . $data['periode_akhir'],
            'koordinator_id'       => $data['koordinator_id'],
            'ketua_tim_id'         => $data['ketua_tim_id'],
        ]);
    }

    /**
     * Update an existing Perencanaan Audit.
     *
     * @param PerencanaanAudit $item
     * @param array $data
     * @return PerencanaanAudit
     */
    public function update(PerencanaanAudit $item, array $data): PerencanaanAudit
    {
        $auditorData = $this->formatAuditors($data['auditor'] ?? []);
        $jenisAudit = MasterJenisAudit::find($data['jenis_audit_id']);

        $item->update([
            'tanggal_surat_tugas'  => $data['tanggal_surat_tugas'],
            'nomor_surat_tugas'    => $data['nomor_surat_tugas'],
            'jenis_audit_id'       => $data['jenis_audit_id'],
            'jenis_audit'          => $jenisAudit ? $jenisAudit->nama_jenis_audit : null,
            'auditor'              => $auditorData,
            'auditee_id'           => $data['auditee'],
            'area_id'              => $data['area_id'] ?? null,
            'ruang_lingkup'        => $data['ruang_lingkup'],
            'tanggal_audit_mulai'  => $data['tanggal_audit_mulai'],
            'tanggal_audit_sampai' => $data['tanggal_audit_sampai'],
            'periode_audit'        => $data['periode_awal'] . ' s/d ' . $data['periode_akhir'],
            'koordinator_id'       => $data['koordinator_id'],
            'ketua_tim_id'         => $data['ketua_tim_id'],
        ]);

        return $item;
    }

    /**
     * Delete a Perencanaan Audit if no related data exists.
     *
     * @param PerencanaanAudit $item
     * @return void
     * @throws \DomainException
     */
    public function delete(PerencanaanAudit $item): void
    {
        $relatedData = [];
        
        if ($item->programKerjaAudit()->count() > 0) {
            $relatedData[] = 'Program Kerja Audit';
        }
        
        if ($item->pelaporanHasilAudit()->count() > 0) {
            $relatedData[] = 'Pelaporan Hasil Audit';
        }
        
        if ($item->walkthroughAudit()->count() > 0) {
            $relatedData[] = 'Walkthrough Audit';
        }
        
        if (!empty($relatedData)) {
            $relatedDataList = implode(', ', $relatedData);
            throw new \DomainException("Tidak dapat menghapus data ini karena masih terkait dengan: {$relatedDataList}. Silakan hapus data terkait terlebih dahulu.");
        }

        $item->delete();
    }

    /**
     * Create a new Jadwal PKPT Audit.
     *
     * @param array $data
     * @return JadwalPkptAudit
     */
    public function createJadwalPkpt(array $data): JadwalPkptAudit
    {
        return JadwalPkptAudit::create([
            'auditee_id' => $data['auditee_id'],
            'jenis_audit' => $data['jenis_audit'],
            'jumlah_auditor' => $data['jumlah_auditor'],
            'tanggal_mulai' => $data['tanggal_mulai'],
            'tanggal_selesai' => $data['tanggal_selesai'],
            'status_approval' => 'pending',
        ]);
    }

    /**
     * Update an existing Jadwal PKPT Audit.
     *
     * @param JadwalPkptAudit $item
     * @param array $data
     * @return JadwalPkptAudit
     */
    public function updateJadwalPkpt(JadwalPkptAudit $item, array $data): JadwalPkptAudit
    {
        $item->update([
            'auditee_id' => $data['auditee_id'],
            'jenis_audit' => $data['jenis_audit'],
            'jumlah_auditor' => $data['jumlah_auditor'],
            'tanggal_mulai' => $data['tanggal_mulai'],
            'tanggal_selesai' => $data['tanggal_selesai'],
        ]);

        return $item;
    }

    /**
     * Delete a Jadwal PKPT Audit.
     *
     * @param JadwalPkptAudit $item
     * @return void
     */
    public function deleteJadwalPkpt(JadwalPkptAudit $item): void
    {
        $item->delete();
    }

    /**
     * Format auditor ID array into "Name - NIP" text format.
     *
     * @param array $auditorIds
     * @return array
     */
    private function formatAuditors(array $auditorIds): array
    {
        $auditorData = [];
        foreach ($auditorIds as $auditorId) {
            if (!empty($auditorId) && is_numeric($auditorId)) {
                $auditor = MasterUser::find($auditorId);
                if ($auditor) {
                    $auditorData[] = $auditor->nama . ' - NIP: ' . $auditor->nip;
                }
            }
        }
        return $auditorData;
    }
}
