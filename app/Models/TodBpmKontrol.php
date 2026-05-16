<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Models\Audit\PkaKontrol;

class TodBpmKontrol extends Model
{
    protected $table   = 'tod_bpm_kontrol';
    protected $guarded = [];

    public function kontrol()
    {
        return $this->belongsTo(PkaKontrol::class, 'pka_kontrol_id');
    }

    public function todBpm()
    {
        return $this->belongsTo(TodBpmAudit::class, 'tod_bpm_audit_id');
    }
}
