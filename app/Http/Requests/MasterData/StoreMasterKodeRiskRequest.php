<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class StoreMasterKodeRiskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kelompok_risiko'        => 'required|string|max:255',
            'kode_risiko'            => 'required|string|max:255|unique:master_kode_risk',
            'kelompok_risiko_detail' => 'required|string|max:255',
            'deskripsi_risiko'       => 'required|string',
        ];
    }
}
