<?php

namespace App\Models\Models\Audit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPkptAudit extends Model
{
    use HasFactory;

    protected $table = 'jadwal_pkpt_audits';
    protected $guarded = [];

    public function auditee()
    {
        return $this->belongsTo(\App\Models\MasterData\MasterAuditee::class, 'auditee_id');
    }
}
