<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MasterData\MasterAuditee;
use App\Models\Models\Audit\ProgramKerjaAudit;
use App\Models\Models\Audit\PelaporanHasilAudit;
use App\Models\WalkthroughAudit;


class PerencanaanAudit extends Model
{
    use HasFactory;
    protected $table = 'perencanaan_audit';
    protected $guarded = [];
    protected $casts = [
        'auditor' => 'array',
        'ruang_lingkup' => 'array',
    ];

    public function auditee()
    {
        return $this->belongsTo(MasterAuditee::class, 'auditee_id');
    }

    public function programKerjaAudit()
    {
        return $this->hasMany(ProgramKerjaAudit::class, 'perencanaan_audit_id');
    }

    public function pelaporanHasilAudit()
    {
        return $this->hasMany(PelaporanHasilAudit::class, 'perencanaan_audit_id');
    }

    public function walkthroughAudit()
    {
        return $this->hasMany(WalkthroughAudit::class, 'perencanaan_audit_id');
    }


} 