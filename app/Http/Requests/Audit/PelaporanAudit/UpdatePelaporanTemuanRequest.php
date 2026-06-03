<?php

namespace App\Http\Requests\Audit\PelaporanAudit;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePelaporanTemuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'hasil_temuan' => 'required|string',
            'kode_aoi_id'  => 'required|exists:master_kode_aoi,id',
            'kode_risk_id' => 'required|exists:master_kode_risk,id',
        ];

        if ($this->has('pelaporan_hasil_audit_id')) {
            $rules['pelaporan_hasil_audit_id'] = 'required|exists:pelaporan_hasil_audit,id';
            $rules['nomor_iss']                = 'required|string';
            $rules['tahun']                    = 'required|digits:4';
        }

        if ($this->has('permasalahan') || $this->has('penyebab') || $this->has('kriteria')) {
            $rules['permasalahan']   = 'required|string';
            $rules['penyebab']       = 'required|string';
            $rules['kriteria']       = 'required|string';
            $rules['signifikan']     = 'required|in:Tinggi,Medium,Rendah';
            $rules['dampak_terjadi'] = 'nullable|string';
            $rules['dampak_potensi'] = 'nullable|string';
        }

        return $rules;
    }
}
