<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MasterData\MasterAuditee;
use App\Models\Models\Audit\ProgramKerjaAudit;
use App\Models\Models\Audit\PelaporanHasilAudit;
use App\Models\WalkthroughAudit;
use App\Traits\FilterableByAuditee;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
class PerencanaanAudit extends Model
{
    use HasUuids;
    use HasFactory, FilterableByAuditee;
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

    public function jenisAudit()
    {
        return $this->belongsTo(\App\Models\MasterData\MasterJenisAudit::class, 'jenis_audit_id');
    }

    public function area()
    {
        return $this->belongsTo(\App\Models\MasterData\MasterArea::class, 'area_id');
    }

    public function koordinator()
    {
        return $this->belongsTo(\App\Models\MasterData\MasterUser::class, 'koordinator_id');
    }

    public function ketuaTim()
    {
        return $this->belongsTo(\App\Models\MasterData\MasterUser::class, 'ketua_tim_id');
    }

    public function realisasiAudit()
    {
        return $this->hasOne(\App\Models\RealisasiAudit::class, 'perencanaan_audit_id');
    }
} 