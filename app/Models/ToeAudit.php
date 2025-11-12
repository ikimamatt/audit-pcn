<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToeAudit extends Model
{
    use HasFactory;

    protected $table = 'toe_audit';
    protected $guarded = [];

    public function evaluasi()
    {
        return $this->hasMany(ToeEvaluasi::class, 'toe_audit_id');
    }

    public function perencanaanAudit()
    {
        return $this->belongsTo(\App\Models\Audit\PerencanaanAudit::class, 'perencanaan_audit_id');
    }
} 