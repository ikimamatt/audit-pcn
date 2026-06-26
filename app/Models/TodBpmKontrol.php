<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Models\Audit\PkaKontrol;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
class TodBpmKontrol extends Model
{
    use HasUuids;
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
