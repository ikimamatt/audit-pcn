<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class StoreMasterAuditeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kd_bidang'           => 'required|string|max:10|unique:master_auditee,kd_bidang',
            'nama_bidang'         => 'required|string|max:255',
            'is_available_for_up' => 'nullable|boolean',
        ];
    }
}
