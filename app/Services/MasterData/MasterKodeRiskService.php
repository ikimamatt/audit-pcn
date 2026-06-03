<?php

namespace App\Services\MasterData;

use App\Models\MasterData\MasterKodeRisk;

class MasterKodeRiskService
{
    /**
     * Create a new Master Kode Risk.
     *
     * @param array $data
     * @return MasterKodeRisk
     */
    public function create(array $data): MasterKodeRisk
    {
        return MasterKodeRisk::create($data);
    }

    /**
     * Update an existing Master Kode Risk.
     *
     * @param MasterKodeRisk $kodeRisk
     * @param array $data
     * @return MasterKodeRisk
     */
    public function update(MasterKodeRisk $kodeRisk, array $data): MasterKodeRisk
    {
        $kodeRisk->update($data);
        return $kodeRisk;
    }

    /**
     * Delete a Master Kode Risk.
     *
     * @param MasterKodeRisk $kodeRisk
     * @return void
     */
    public function delete(MasterKodeRisk $kodeRisk): void
    {
        $kodeRisk->delete();
    }
}
