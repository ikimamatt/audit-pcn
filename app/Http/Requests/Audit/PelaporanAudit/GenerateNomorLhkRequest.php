<?php

namespace App\Http\Requests\Audit\PelaporanAudit;

use Illuminate\Foundation\Http\FormRequest;

class GenerateNomorLhkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jenis_lha_lhk'  => 'required|in:LHA,LHK',
            'jenis_audit_id' => 'required|exists:master_jenis_audit,id',
            'kode_spi'       => 'required|string',
        ];
    }
}
