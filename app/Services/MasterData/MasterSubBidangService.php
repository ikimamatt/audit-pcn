<?php

namespace App\Services\MasterData;

use App\Models\MasterData\MasterSubBidang;

class MasterSubBidangService
{
    /**
     * Create a new Master Sub Bidang.
     *
     * @param array $data
     * @return MasterSubBidang
     */
    public function create(array $data): MasterSubBidang
    {
        return MasterSubBidang::create($data);
    }

    /**
     * Update an existing Master Sub Bidang.
     *
     * @param MasterSubBidang $subBidang
     * @param array $data
     * @return MasterSubBidang
     */
    public function update(MasterSubBidang $subBidang, array $data): MasterSubBidang
    {
        $subBidang->update($data);
        return $subBidang;
    }

    /**
     * Delete a Master Sub Bidang.
     *
     * @param MasterSubBidang $subBidang
     * @return void
     */
    public function delete(MasterSubBidang $subBidang): void
    {
        $subBidang->delete();
    }
}
