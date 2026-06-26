<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Models\Audit\PkaRisiko;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
class TodBpmRisiko extends Model
{
    use HasUuids;
    protected $table    = 'tod_bpm_risiko';
    protected $guarded  = [];

    public function risiko()
    {
        return $this->belongsTo(PkaRisiko::class, 'pka_risiko_id');
    }

    public function todBpm()
    {
        return $this->belongsTo(TodBpmAudit::class, 'tod_bpm_audit_id');
    }
}
