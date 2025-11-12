<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringTindakLanjut extends Model
{
    use HasFactory;

    protected $table = 'monitoring_tindak_lanjut';

    protected $fillable = [
        'objek_pemeriksaan',
        'aoi_count',
        'rekomendasi_count',
        'tindak_lanjut_target',
        'tindak_lanjut_real',
        'sisa_target',
        'sisa_real',
        'bulan_jan_target',
        'bulan_jan_real',
        'bulan_feb_target',
        'bulan_feb_real',
        'bulan_mar_target',
        'bulan_mar_real',
        'bulan_apr_target',
        'bulan_apr_real',
        'bulan_mei_target',
        'bulan_mei_real',
        'bulan_jun_target',
        'bulan_jun_real',
        'bulan_jul_target',
        'bulan_jul_real',
        'bulan_ags_target',
        'bulan_ags_real',
        'bulan_sep_target',
        'bulan_sep_real',
        'bulan_okt_target',
        'bulan_okt_real',
        'is_category',
        'is_total',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'is_category' => 'boolean',
        'is_total' => 'boolean',
    ];
}

