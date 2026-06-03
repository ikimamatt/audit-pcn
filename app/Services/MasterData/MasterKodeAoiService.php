<?php

namespace App\Services\MasterData;

use App\Models\MasterData\MasterKodeAoi;

class MasterKodeAoiService
{
    /**
     * Create a new Master Kode Aoi.
     *
     * @param array $data
     * @return MasterKodeAoi
     */
    public function create(array $data): MasterKodeAoi
    {
        return MasterKodeAoi::create($data);
    }

    /**
     * Update an existing Master Kode Aoi.
     *
     * @param MasterKodeAoi $kodeAoi
     * @param array $data
     * @return MasterKodeAoi
     */
    public function update(MasterKodeAoi $kodeAoi, array $data): MasterKodeAoi
    {
        $kodeAoi->update($data);
        return $kodeAoi;
    }

    /**
     * Delete a Master Kode Aoi.
     *
     * @param MasterKodeAoi $kodeAoi
     * @return void
     */
    public function delete(MasterKodeAoi $kodeAoi): void
    {
        $kodeAoi->delete();
    }
}
