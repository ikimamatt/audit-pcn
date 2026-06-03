<?php

namespace App\Http\Requests\Audit\PelaksanaanAudit;

use Illuminate\Foundation\Http\FormRequest;

class StoreTodBpmEvaluasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tod_bpm_audit_id' => 'required|exists:tod_bpm_audit,id',
            'hasil_evaluasi'   => 'required|string',
        ];
    }
}
