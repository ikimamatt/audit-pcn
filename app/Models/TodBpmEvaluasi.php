<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TodBpmEvaluasi extends Model
{
    use HasFactory;

    protected $table = 'tod_bpm_evaluasi';
    protected $guarded = [];

    public function bpm()
    {
        return $this->belongsTo(TodBpmAudit::class, 'tod_bpm_audit_id');
    }
}
