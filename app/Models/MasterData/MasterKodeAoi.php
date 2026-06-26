<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
class MasterKodeAoi extends Model
{
    use HasUuids;
    use HasFactory;
    protected $table = 'master_kode_aoi';
    protected $guarded = [];
} 