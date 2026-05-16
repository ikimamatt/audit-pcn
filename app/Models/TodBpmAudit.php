<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Models\Audit\PkaRisiko;
use App\Models\Models\Audit\PkaKontrol;

class TodBpmAudit extends Model
{
    use HasFactory;

    protected $table   = 'tod_bpm_audit';
    protected $guarded = [];

    public function evaluasi()
    {
        return $this->hasMany(TodBpmEvaluasi::class, 'tod_bpm_audit_id');
    }

    public function perencanaanAudit()
    {
        return $this->belongsTo(\App\Models\Audit\PerencanaanAudit::class, 'perencanaan_audit_id');
    }

    /** Risiko PKA yang dipilih di TOD ini (via pivot tod_bpm_risiko) */
    public function selectedRisiko()
    {
        return $this->hasMany(TodBpmRisiko::class, 'tod_bpm_audit_id');
    }

    /** Kontrol PKA yang dipilih di TOD ini (via pivot tod_bpm_kontrol) */
    public function selectedKontrol()
    {
        return $this->hasMany(TodBpmKontrol::class, 'tod_bpm_audit_id');
    }

    /** Many-to-many langsung ke pka_risiko */
    public function pkaRisiko()
    {
        return $this->belongsToMany(PkaRisiko::class, 'tod_bpm_risiko', 'tod_bpm_audit_id', 'pka_risiko_id');
    }

    /** Many-to-many langsung ke pka_kontrol */
    public function pkaKontrol()
    {
        return $this->belongsToMany(PkaKontrol::class, 'tod_bpm_kontrol', 'tod_bpm_audit_id', 'pka_kontrol_id');
    }
}
