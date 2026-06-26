<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
class MasterSubBidang extends Model
{
    use HasUuids;
    use HasFactory;
    protected $table = 'master_sub_bidang';
    protected $fillable = ['nama', 'master_bidang_id'];

    public function bidang()
    {
        return $this->belongsTo(MasterAuditee::class, 'master_bidang_id');
    }
}
