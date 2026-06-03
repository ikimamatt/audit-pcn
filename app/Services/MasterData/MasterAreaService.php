<?php

namespace App\Services\MasterData;

use App\Models\MasterData\MasterArea;

class MasterAreaService
{
    /**
     * Create a new Master Area.
     *
     * @param array $data
     * @return MasterArea
     */
    public function create(array $data): MasterArea
    {
        return MasterArea::create($data);
    }

    /**
     * Update an existing Master Area.
     *
     * @param MasterArea $area
     * @param array $data
     * @return MasterArea
     */
    public function update(MasterArea $area, array $data): MasterArea
    {
        $area->update($data);
        return $area;
    }

    /**
     * Delete a Master Area.
     *
     * @param MasterArea $area
     * @return void
     */
    public function delete(MasterArea $area): void
    {
        $area->delete();
    }
}
