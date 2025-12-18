<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class RealisasiAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'perencanaan_audit_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'file_undangan',
        'file_absensi',
        'status_approval',
        'approved_by',
        'approved_at',
        'rejection_reason',
        // Level 1 approval fields
        'approved_by_level1',
        'approved_at_level1',
        'rejected_by_level1',
        'rejected_at_level1',
        'rejection_reason_level1',
        // Level 2 approval fields
        'approved_by_level2',
        'approved_at_level2',
        'rejected_by_level2',
        'rejected_at_level2',
        'rejection_reason_level2',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'approved_at' => 'datetime',
    ];

    public function perencanaanAudit()
    {
        return $this->belongsTo(\App\Models\Audit\PerencanaanAudit::class, 'perencanaan_audit_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scope untuk data yang sudah approved
    public function scopeApproved($query)
    {
        return $query->where('status_approval', 'approved');
    }

    // Scope untuk data pending
    public function scopePending($query)
    {
        return $query->where('status_approval', 'pending');
    }

    // Scope untuk data rejected
    public function scopeRejected($query)
    {
        return $query->where('status_approval', 'rejected');
    }
}
