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
        'po_audit_konsul',
        'kode_spi',
        'nomor_urut',
        'tahun',
        'status_approval',
        'approved_by',
        'approved_at',
        'alasan_reject',
        'nomor_iss', // Bisa null karena data ISS disimpan di tabel terpisah
    ];

    public function perencanaanAudit()
    {
        return $this->belongsTo(\App\Models\Audit\PerencanaanAudit::class, 'perencanaan_audit_id');
    }

    public function temuan()
    {
        return $this->hasMany(PelaporanTemuan::class, 'pelaporan_hasil_audit_id');
    }

    public function approver()
    {
        return $this->belongsTo(\App\Models\MasterData\MasterUser::class, 'approved_by');
    }

}
