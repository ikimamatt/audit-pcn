<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TodBpmAudit extends Model
{
    use HasFactory;

    protected $table = 'tod_bpm_audit';
    protected $guarded = [];

    public function evaluasi()
    {
        return $this->hasMany(TodBpmEvaluasi::class, 'tod_bpm_audit_id');
    }

    public function perencanaanAudit()
    {
        return $this->belongsTo(\App\Models\Audit\PerencanaanAudit::class, 'perencanaan_audit_id');
    }
}
