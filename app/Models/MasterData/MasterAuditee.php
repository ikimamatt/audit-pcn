<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
class MasterAuditee extends Model
{
    use HasUuids;
    use HasFactory;
    protected $table = 'master_auditee';
    protected $fillable = ['kd_bidang', 'nama_bidang', 'is_available_for_up'];

    /**
     * Backward compatibility: $auditee->divisi returns nama_bidang
     * Menjaga agar 50+ file yang mengakses ->divisi tetap berjalan.
     */
    protected function divisi(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->nama_bidang,
        );
    }

    /**
     * Backward compatibility: $auditee->direktorat returns null
     * Kolom ini sudah dihapus dari migration.
     */
    protected function direktorat(): Attribute
    {
        return Attribute::make(
            get: fn () => null,
        );
    }

    /**
     * Backward compatibility: $auditee->divisi_cabang returns null
     * Kolom ini sudah dihapus dari migration.
     */
    protected function divisiCabang(): Attribute
    {
        return Attribute::make(
            get: fn () => null,
        );
    }

    public function perencanaanAudit()
    {
        return $this->hasMany(\App\Models\Audit\PerencanaanAudit::class, 'auditee_id');
    }

    public function subBidang()
    {
        return $this->hasMany(MasterSubBidang::class, 'master_bidang_id');
    }
}