<?php

namespace App\Models\Audit;

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
        'permasalahan',
        'penyebab',
        'kriteria',
        'dampak_terjadi',
        'dampak_potensi',
        'signifikan',
        'status_approval',
        'approved_by',
        'approved_at',
        'alasan_reject',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
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

    public function approvedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }
}


