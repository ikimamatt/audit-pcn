<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class MasterUser extends Authenticatable
{
    use HasFactory;
    protected $table = 'master_user';
    protected $guarded = [];

    /**
     * The attributes that should be used for authentication.
     *
     * @var string
     */
    protected $username = 'username';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function akses()
    {
        return $this->belongsTo(MasterAksesUser::class, 'master_akses_user_id');
    }

    public function auditee()
    {
        return $this->belongsTo(MasterAuditee::class, 'master_auditee_id');
    }
}
