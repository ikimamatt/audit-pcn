<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenutupLhaTindakLanjut extends Model
{
    use HasFactory;
    protected $table = 'penutup_lha_tindak_lanjut';
    protected $fillable = [
        'penutup_lha_rekomendasi_id',
        'real_waktu',
        'komentar',
        'file_eviden',
        'status_tindak_lanjut',
    ];

    public function rekomendasi()
    {
        return $this->belongsTo(PenutupLhaRekomendasi::class, 'penutup_lha_rekomendasi_id');
    }
} 