<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExitMeetingUpload extends Model
{
    use HasFactory;
    protected $table = 'exit_meeting_uploads';
    protected $fillable = [
        'tanggal_exit_meeting',
        'auditee_id',
        'file_undangan',
        'file_absensi',
        'status_approval_undangan',
        'approved_by_undangan',
        'approved_at_undangan',
        'status_approval_absensi',
        'approved_by_absensi',
        'approved_at_absensi',
        'approve',
        'approve_at',
    ];

    protected $casts = [
        'tanggal_exit_meeting' => 'date',
        'approved_at_undangan' => 'datetime',
        'approved_at_absensi' => 'datetime',
        'approve' => 'boolean',
        'approve_at' => 'datetime',
    ];

    public function auditee()
    {
        return $this->belongsTo(\App\Models\MasterData\MasterAuditee::class, 'auditee_id');
    }

    public function approvedByUndangan()
    {
        return $this->belongsTo(\App\Models\MasterData\MasterUser::class, 'approved_by_undangan');
    }

    public function approvedByAbsensi()
    {
        return $this->belongsTo(\App\Models\MasterData\MasterUser::class, 'approved_by_absensi');
    }

    public function approvedByFinal()
    {
        return $this->belongsTo(\App\Models\MasterData\MasterUser::class, 'approved_by'); // Assuming 'approved_by' will be used for the new 'approve' column
    }

    public function lhaLhk()
    {
        return $this->hasOne(\App\Models\LhaLhkUpload::class, 'id', 'id');
    }
    public function notaDinas()
    {
        return $this->hasOne(\App\Models\NotaDinasUpload::class, 'id', 'id');
    }
}
