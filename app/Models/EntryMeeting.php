<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Models\Audit\ProgramKerjaAudit;

class EntryMeeting extends Model
{
    use HasFactory;
    protected $table = 'entry_meeting';
    protected $guarded = [];

    public function auditee()
    {
        return $this->belongsTo(\App\Models\MasterData\MasterAuditee::class, 'auditee_id');
    }

    public function programKerjaAudit()
    {
        return $this->belongsTo(ProgramKerjaAudit::class, 'program_kerja_audit_id');
    }
} 