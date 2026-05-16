<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Models\Audit\PkaKontrol;

class ToeKontrol extends Model
{
    protected $table   = 'toe_kontrol';
    protected $guarded = [];

    public function kontrol()
    {
        return $this->belongsTo(PkaKontrol::class, 'pka_kontrol_id');
    }

    public function toeAudit()
    {
        return $this->belongsTo(ToeAudit::class, 'toe_audit_id');
    }
}
