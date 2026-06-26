<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
class EmailNotificationLog extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'email_notification_logs';
    protected $fillable = [
        'penutup_lha_rekomendasi_id',
        'master_user_id',
        'trigger_type',
        'sent_by',
        'status',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function rekomendasi()
    {
        return $this->belongsTo(PenutupLhaRekomendasi::class, 'penutup_lha_rekomendasi_id');
    }

    public function penerima()
    {
        return $this->belongsTo(\App\Models\MasterData\MasterUser::class, 'master_user_id');
    }

    public function pengirim()
    {
        return $this->belongsTo(\App\Models\MasterData\MasterUser::class, 'sent_by');
    }
}
