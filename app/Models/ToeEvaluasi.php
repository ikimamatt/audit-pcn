<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
class ToeEvaluasi extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'toe_evaluasi';
    protected $guarded = [];

    public function toe()
    {
        return $this->belongsTo(ToeAudit::class, 'toe_audit_id');
    }
} 