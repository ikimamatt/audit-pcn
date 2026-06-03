<?php

namespace App\Services\MasterData;

use App\Models\MasterData\MasterJenisAudit;

class MasterJenisAuditService
{
    /**
     * Create a new Master Jenis Audit.
     *
     * @param array $data
     * @return MasterJenisAudit
     */
    public function create(array $data): MasterJenisAudit
    {
        return MasterJenisAudit::create($data);
    }

    /**
     * Update an existing Master Jenis Audit.
     *
     * @param MasterJenisAudit $jenisAudit
     * @param array $data
     * @return MasterJenisAudit
     */
    public function update(MasterJenisAudit $jenisAudit, array $data): MasterJenisAudit
    {
        $jenisAudit->update($data);
        return $jenisAudit;
    }

    /**
     * Delete a Master Jenis Audit.
     *
     * @param MasterJenisAudit $jenisAudit
     * @return void
     */
    public function delete(MasterJenisAudit $jenisAudit): void
    {
        $jenisAudit->delete();
    }
}
