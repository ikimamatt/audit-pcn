<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenutupLhaRekomendasi extends Model
{
    use HasFactory;
    protected $table = 'penutup_lha_rekomendasi';
    protected $guarded = [];
    protected $fillable = [
        'pelaporan_isi_lha_id', // Actually stores pelaporan_temuan_id for compatibility
        'rekomendasi',
        'rencana_aksi',
        'eviden_rekomendasi',
        'pic_rekomendasi',
        'target_waktu',
        'real_waktu',
        'komentar',
        'file_eviden',
        'status_tindak_lanjut',
        'status_approval',
        'approved_by',
        'approved_at',
        'alasan_reject',
    ];

    // Relationship to PelaporanTemuan (ISS data)
    public function temuan()
    {
        return $this->belongsTo(\App\Models\Audit\PelaporanTemuan::class, 'pelaporan_isi_lha_id');
    }

    // Legacy relationship (kept for compatibility)
    // public function isiLha()
    // {
    //     return $this->belongsTo(\App\Models\Models\Audit\PelaporanIsiLha::class, 'pelaporan_isi_lha_id');
    // }

    public function tindakLanjut()
    {
        return $this->hasMany(\App\Models\PenutupLhaTindakLanjut::class, 'penutup_lha_rekomendasi_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(\App\Models\MasterData\MasterUser::class, 'approved_by');
    }

    public function picUsers()
    {
        return $this->belongsToMany(\App\Models\MasterData\MasterUser::class, 'penutup_lha_rekomendasi_pic', 'penutup_lha_rekomendasi_id', 'master_user_id');
    }
} 