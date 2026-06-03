<?php

namespace App\Services\MasterData;

use App\Models\MasterData\MasterAuditee;
use Illuminate\Database\Eloquent\Collection;

class MasterAuditeeService
{
    /**
     * Create a new Master Auditee.
     *
     * @param array $data
     * @return MasterAuditee
     */
    public function create(array $data): MasterAuditee
    {
        return MasterAuditee::create($data);
    }

    /**
     * Update an existing Master Auditee.
     *
     * @param MasterAuditee $auditee
     * @param array $data
     * @return MasterAuditee
     */
    public function update(MasterAuditee $auditee, array $data): MasterAuditee
    {
        $auditee->update($data);
        return $auditee;
    }

    /**
     * Delete a Master Auditee.
     *
     * @param MasterAuditee $auditee
     * @return void
     */
    public function delete(MasterAuditee $auditee): void
    {
        $auditee->delete();
    }

    /**
     * Get sub-bidang for an auditee.
     *
     * @param MasterAuditee $auditee
     * @return Collection
     */
    public function getSubBidang(MasterAuditee $auditee): Collection
    {
        return $auditee->subBidang()->orderBy('nama')->get();
    }
}
