<?php

namespace App\Models\Models\Audit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
class PkaKontrol extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'pka_kontrol';
    protected $guarded = [];

    public function risiko()
    {
        return $this->belongsTo(PkaRisiko::class, 'pka_risiko_id');
    }
}
