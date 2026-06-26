<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
class MasterRegion extends Model
{
    use HasUuids;
    use HasFactory, SoftDeletes;

    protected $table = 'master_region';
    protected $guarded = [];
}
