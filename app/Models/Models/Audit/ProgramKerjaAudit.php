<?php

namespace App\Models\Models\Audit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EntryMeeting;
use App\Models\WalkthroughAudit;

class ProgramKerjaAudit extends Model
{
    use HasFactory;

    protected $table = 'program_kerja_audit';
    protected $guarded = [];

    public function perencanaanAudit()
    {
        return $this->belongsTo(\App\Models\Audit\PerencanaanAudit::class, 'perencanaan_audit_id');
    }

    public function risks()
    {
        return $this->hasMany(PkaRiskBasedAudit::class, 'program_kerja_audit_id');
    }

    public function milestones()
    {
        return $this->hasMany(PkaMilestone::class, 'program_kerja_audit_id');
    }

    public function dokumen()
    {
        return $this->hasMany(PkaDokumen::class, 'program_kerja_audit_id');
    }

    public function entryMeeting()
    {
        return $this->hasOne(EntryMeeting::class, 'program_kerja_audit_id');
    }

    public function walkthroughAudit()
    {
        return $this->hasOne(WalkthroughAudit::class, 'program_kerja_audit_id');
    }
}
