<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterArea extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_area';
    protected $guarded = [];

    public function region()
    {
        return $this->belongsTo(MasterRegion::class, 'kd_region', 'kd_region');
    }
}
