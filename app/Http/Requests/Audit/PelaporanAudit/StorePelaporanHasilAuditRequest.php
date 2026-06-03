<?php

namespace App\Http\Requests\Audit\PelaporanAudit;

use Illuminate\Foundation\Http\FormRequest;

class StorePelaporanHasilAuditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'perencanaan_audit_id' => 'required|exists:perencanaan_audit,id',
            'nomor_lha_lhk'        => 'required|string',
            'jenis_lha_lhk'        => 'required|in:LHA,LHK',
            'kode_spi'             => 'required|string',
            'jenis_audit_id'       => 'nullable|exists:master_jenis_audit,id',
            'hasil_temuan'         => 'required|array',
            'hasil_temuan.*'       => 'required|string',
            'kode_aoi_id'          => 'required|array',
            'kode_aoi_id.*'        => 'required|exists:master_kode_aoi,id',
            'kode_risk_id'         => 'required|array',
            'kode_risk_id.*'       => 'required|exists:master_kode_risk,id',
            'nomor_iss'            => 'required|array',
            'nomor_iss.*'          => 'required|string',
            'nomor_urut_iss'       => 'required|array',
            'nomor_urut_iss.*'     => 'required|integer',
            'permasalahan'         => 'required|array',
            'permasalahan.*'       => 'required|string',
            'penyebab'             => 'required|array',
            'penyebab.*'           => 'required|string',
            'kriteria'             => 'required|array',
            'kriteria.*'           => 'required|string',
            'dampak_terjadi'       => 'nullable|array',
            'dampak_terjadi.*'     => 'nullable|string',
            'dampak_potensi'       => 'nullable|array',
            'dampak_potensi.*'     => 'nullable|string',
            'signifikan'           => 'required|array',
            'signifikan.*'         => 'required|in:Tinggi,Medium,Rendah',
        ];
    }
}
