<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Models\Audit\PkaRisiko;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
class ToeRisiko extends Model
{
    use HasUuids;
    protected $table   = 'toe_risiko';
    protected $guarded = [];

    public function risiko()
    {
        return $this->belongsTo(PkaRisiko::class, 'pka_risiko_id');
    }

    public function toeAudit()
    {
        return $this->belongsTo(ToeAudit::class, 'toe_audit_id');
    }
}
