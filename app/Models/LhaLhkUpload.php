<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LhaLhkUpload extends Model
{
    use HasFactory;
    protected $table = 'lha_lhk_uploads';
    protected $fillable = [
        'pelaporan_hasil_audit_id',
        'file_lha_lhk',
        'status_approval',
        'approved_by',
        'approved_at',
        'approve',
        'approve_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'approve' => 'boolean',
        'approve_at' => 'datetime',
    ];

    public function pelaporanHasilAudit()
    {
        return $this->belongsTo(\App\Models\Models\Audit\PelaporanHasilAudit::class, 'pelaporan_hasil_audit_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(\App\Models\MasterData\MasterUser::class, 'approved_by');
    }
}
