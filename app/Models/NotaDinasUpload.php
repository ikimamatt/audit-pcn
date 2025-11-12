<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaDinasUpload extends Model
{
    use HasFactory;
    protected $table = 'nota_dinas_uploads';
    protected $guarded = [];
    protected $fillable = [
        'pelaporan_hasil_audit_id',
        'file_nota_dinas',
        'tujuan_nota_dinas',
    ];

    public function pelaporanHasilAudit()
    {
        return $this->belongsTo(\App\Models\Models\Audit\PelaporanHasilAudit::class, 'pelaporan_hasil_audit_id');
    }
} 