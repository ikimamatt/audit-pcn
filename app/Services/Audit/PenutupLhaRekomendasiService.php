<?php

namespace App\Services\Audit;

use App\Models\PenutupLhaRekomendasi;
use App\Models\MasterData\MasterUser;
use Illuminate\Support\Facades\DB;

class PenutupLhaRekomendasiService
{
    /**
     * Create a new Penutup LHA Recommendation.
     *
     * @param array $data
     * @return PenutupLhaRekomendasi
     */
    public function create(array $data): PenutupLhaRekomendasi
    {
        return DB::transaction(function () use ($data) {
            $picRekomendasi = $this->formatPicRekomendasi(
                $data['pic_business_contact'] ?? null,
                $data['pic_approval_1_spi'] ?? null,
                $data['pic_approval_2_spi'] ?? null
            );

            $rekomendasi = PenutupLhaRekomendasi::create([
                'pelaporan_isi_lha_id' => $data['pelaporan_isi_lha_id'],
                'rekomendasi'          => $data['rekomendasi'],
                'rencana_aksi'         => $data['rencana_aksi'],
                'eviden_rekomendasi'   => $data['eviden_rekomendasi'],
                'target_waktu'         => $data['target_waktu'],
                'pic_rekomendasi'      => $picRekomendasi,
            ]);

            $rekomendasi->picUsers()->attach([
                $data['pic_business_contact'] => ['pic_type' => 'business_contact'],
                $data['pic_approval_1_spi']   => ['pic_type' => 'approval_1_spi'],
                $data['pic_approval_2_spi']   => ['pic_type' => 'approval_2_spi'],
            ]);

            return $rekomendasi;
        });
    }

    /**
     * Update an existing Penutup LHA Recommendation.
     *
     * @param PenutupLhaRekomendasi $item
     * @param array $data
     * @return PenutupLhaRekomendasi
     */
    public function update(PenutupLhaRekomendasi $item, array $data): PenutupLhaRekomendasi
    {
        return DB::transaction(function () use ($item, $data) {
            $picRekomendasi = $this->formatPicRekomendasi(
                $data['pic_business_contact'] ?? null,
                $data['pic_approval_1_spi'] ?? null,
                $data['pic_approval_2_spi'] ?? null
            );

            $item->update([
                'pelaporan_isi_lha_id' => $data['pelaporan_isi_lha_id'],
                'rekomendasi'          => $data['rekomendasi'],
                'rencana_aksi'         => $data['rencana_aksi'],
                'eviden_rekomendasi'   => $data['eviden_rekomendasi'],
                'target_waktu'         => $data['target_waktu'],
                'pic_rekomendasi'      => $picRekomendasi,
            ]);

            $item->picUsers()->sync([
                $data['pic_business_contact'] => ['pic_type' => 'business_contact'],
                $data['pic_approval_1_spi']   => ['pic_type' => 'approval_1_spi'],
                $data['pic_approval_2_spi']   => ['pic_type' => 'approval_2_spi'],
            ]);

            return $item;
        });
    }

    /**
     * Delete a Penutup LHA Recommendation.
     *
     * @param PenutupLhaRekomendasi $item
     * @return void
     */
    public function delete(PenutupLhaRekomendasi $item): void
    {
        DB::transaction(function () use ($item) {
            $item->picUsers()->detach();
            $item->delete();
        });
    }

    /**
     * Format PIC IDs into a concatenated string representation.
     *
     * @param int|null $bcId
     * @param int|null $ap1Id
     * @param int|null $ap2Id
     * @return string
     */
    private function formatPicRekomendasi(?int $bcId, ?int $ap1Id, ?int $ap2Id): string
    {
        $picBusinessContact = MasterUser::with('auditee')->find($bcId);
        $picApproval1 = MasterUser::with('auditee')->find($ap1Id);
        $picApproval2 = MasterUser::with('auditee')->find($ap2Id);
        
        $picRekomendasiList = [];
        if ($picBusinessContact) {
            $picRekomendasiList[] = 'BUSINESS CONTACT: ' . $picBusinessContact->nama . ' - ' . ($picBusinessContact->auditee->divisi ?? '-');
        }
        if ($picApproval1) {
            $picRekomendasiList[] = 'BUSINESS REVIEWER 1: ' . $picApproval1->nama . ' - ' . ($picApproval1->auditee->divisi ?? '-');
        }
        if ($picApproval2) {
            $picRekomendasiList[] = 'BUSINESS REVIEWER 2: ' . $picApproval2->nama . ' - ' . ($picApproval2->auditee->divisi ?? '-');
        }
        return implode(' | ', $picRekomendasiList);
    }
}
