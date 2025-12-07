<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterJenisAudit extends Model
{
    use HasFactory;
    protected $table = 'master_jenis_audit';
    protected $guarded = [];
    
    public function perencanaanAudit()
    {
        return $this->hasMany(\App\Models\Audit\PerencanaanAudit::class, 'jenis_audit_id');
    }
}

