<?php

namespace App\Models\Models\Audit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
class PkaProsesBisnis extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'pka_proses_bisnis';
    protected $guarded = [];

    public function programKerjaAudit()
    {
        return $this->belongsTo(ProgramKerjaAudit::class, 'program_kerja_audit_id');
    }

    public function risikoList()
    {
        return $this->hasMany(PkaRisiko::class, 'pka_proses_bisnis_id')->orderBy('urutan');
    }
}
