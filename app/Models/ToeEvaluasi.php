<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToeEvaluasi extends Model
{
    use HasFactory;

    protected $table = 'toe_evaluasi';
    protected $guarded = [];

    public function toe()
    {
        return $this->belongsTo(ToeAudit::class, 'toe_audit_id');
    }
} 