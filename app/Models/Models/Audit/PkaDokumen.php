<?php

namespace App\Models\Models\Audit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PkaDokumen extends Model
{
    use HasFactory;

    protected $table = 'pka_dokumen';
    protected $guarded = [];

    public function programKerjaAudit()
    {
        return $this->belongsTo(ProgramKerjaAudit::class, 'program_kerja_audit_id');
    }
}
