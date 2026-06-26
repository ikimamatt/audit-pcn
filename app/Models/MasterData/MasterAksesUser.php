<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
class MasterAksesUser extends Model
{
    use HasUuids;
    use HasFactory;
    protected $table = 'master_akses_user';
    protected $guarded = [];
} 