<?php

namespace App\Http\Requests\Audit\PelaporanAudit;

use Illuminate\Foundation\Http\FormRequest;

class GenerateNomorIssRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nomor_lha_lhk' => 'required|string',
            'kode_spi'      => 'required|in:SPI.01.02,SPI.01.03,SPI.01.04',
            'kode_aoi_id'   => 'required|exists:master_kode_aoi,id',
            'kode_risk_id'  => 'required|exists:master_kode_risk,id',
        ];
    }
}
