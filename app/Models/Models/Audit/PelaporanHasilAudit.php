<?php

namespace App\Models\Models\Audit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelaporanHasilAudit extends Model
{
    use HasFactory;

    protected $table = 'pelaporan_hasil_audit';

    protected $fillable = [
        'perencanaan_audit_id',
        'nomor_lha_lhk',
        'jenis_lha_lhk',
        'kode_spi',
        'jenis_audit_id',
        'nomor_urut',
        'tahun',
        'status_approval',
        'approved_by',
        'approved_at',
        'alasan_reject',
        'nomor_iss', // Bisa null karena data ISS disimpan di tabel terpisah
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

    public function perencanaanAudit()
    {
        return $this->belongsTo(\App\Models\Audit\PerencanaanAudit::class, 'perencanaan_audit_id');
    }

    public function temuan()
    {
        return $this->hasMany(\App\Models\Audit\PelaporanTemuan::class, 'pelaporan_hasil_audit_id');
    }

    public function approver()
    {
        return $this->belongsTo(\App\Models\MasterData\MasterUser::class, 'approved_by');
    }

}
