<?php

namespace App\Models\Models\Audit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelaporanTemuan extends Model
{
    use HasFactory;

    protected $table = 'pelaporan_temuan';

    protected $fillable = [
        'pelaporan_hasil_audit_id',
        'hasil_temuan',
        'kode_aoi_id',
        'kode_risk_id',
        'nomor_iss',
        'nomor_urut_iss',
        'tahun',
        'status_approval',
        'approved_by',
        'approved_at',
    ];

    public function pelaporanHasilAudit()
    {
        return $this->belongsTo(PelaporanHasilAudit::class, 'pelaporan_hasil_audit_id');
    }

    public function kodeAoi()
    {
        return $this->belongsTo(\App\Models\MasterData\MasterKodeAoi::class, 'kode_aoi_id');
    }

    public function kodeRisk()
    {
        return $this->belongsTo(\App\Models\MasterData\MasterKodeRisk::class, 'kode_risk_id');
    }

    public function approver()
    {
        return $this->belongsTo(\App\Models\MasterData\MasterUser::class, 'approved_by');
    }
}
