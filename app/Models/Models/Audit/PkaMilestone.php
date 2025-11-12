<?php

namespace App\Models\Models\Audit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PkaMilestone extends Model
{
    use HasFactory;

    protected $table = 'pka_milestone';
    protected $guarded = [];

    public function programKerjaAudit()
    {
        return $this->belongsTo(ProgramKerjaAudit::class, 'program_kerja_audit_id');
    }
}
