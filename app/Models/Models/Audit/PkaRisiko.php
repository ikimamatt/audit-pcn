<?php

namespace App\Models\Models\Audit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PkaRisiko extends Model
{
    use HasFactory;

    protected $table = 'pka_risiko';
    protected $guarded = [];

    public function prosesBisnis()
    {
        return $this->belongsTo(PkaProsesBisnis::class, 'pka_proses_bisnis_id');
    }

    public function kontrolList()
    {
        return $this->hasMany(PkaKontrol::class, 'pka_risiko_id')->orderBy('urutan');
    }
}
