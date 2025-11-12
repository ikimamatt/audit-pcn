<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterAuditee extends Model
{
    use HasFactory;
    protected $table = 'master_auditee';
    protected $guarded = [];
    
    public function perencanaanAudit()
    {
        return $this->hasMany(\App\Models\Audit\PerencanaanAudit::class, 'auditee_id');
    }
} 