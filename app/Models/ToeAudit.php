<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Models\Audit\PkaRisiko;
use App\Models\Models\Audit\PkaKontrol;
use App\Traits\FilterableByAuditee;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
class ToeAudit extends Model
{
    use HasUuids;
    use HasFactory, FilterableByAuditee;

    protected $table   = 'toe_audit';
    protected $guarded = [];

    public function evaluasi()
    {
        return $this->hasMany(ToeEvaluasi::class, 'toe_audit_id');
    }

    public function perencanaanAudit()
    {
        return $this->belongsTo(\App\Models\Audit\PerencanaanAudit::class, 'perencanaan_audit_id');
    }

    /** Risiko PKA yang dipilih di TOE ini (via pivot toe_risiko) */
    public function selectedRisiko()
    {
        return $this->hasMany(ToeRisiko::class, 'toe_audit_id');
    }

    /** Kontrol PKA yang dipilih di TOE ini (via pivot toe_kontrol) */
    public function selectedKontrol()
    {
        return $this->hasMany(ToeKontrol::class, 'toe_audit_id');
    }

    /** Many-to-many langsung ke pka_risiko */
    public function pkaRisiko()
    {
        return $this->belongsToMany(PkaRisiko::class, 'toe_risiko', 'toe_audit_id', 'pka_risiko_id');
    }

    /** Many-to-many langsung ke pka_kontrol */
    public function pkaKontrol()
    {
        return $this->belongsToMany(PkaKontrol::class, 'toe_kontrol', 'toe_audit_id', 'pka_kontrol_id');
    }
}