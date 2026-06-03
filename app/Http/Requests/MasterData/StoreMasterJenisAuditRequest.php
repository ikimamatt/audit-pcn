<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class StoreMasterJenisAuditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_jenis_audit' => 'required|string|max:255',
            'kode'             => 'nullable|string|max:255',
        ];
    }
}
