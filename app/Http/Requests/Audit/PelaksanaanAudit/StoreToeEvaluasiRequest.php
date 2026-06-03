<?php

namespace App\Http\Requests\Audit\PelaksanaanAudit;

use Illuminate\Foundation\Http\FormRequest;

class StoreToeEvaluasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'toe_audit_id'   => 'required|exists:toe_audit,id',
            'hasil_evaluasi' => 'required|string',
        ];
    }
}
