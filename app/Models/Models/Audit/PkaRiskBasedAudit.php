<?php

namespace App\Models\Models\Audit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
class PkaRiskBasedAudit extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'pka_risk_based_audit';
    protected $guarded = [];

    public function programKerjaAudit()
    {
        return $this->belongsTo(ProgramKerjaAudit::class, 'program_kerja_audit_id');
    }
}
