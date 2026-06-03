<?php

namespace App\Http\Requests\Audit\PelaksanaanAudit;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWalkthroughRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'program_kerja_audit_id'   => 'required|exists:program_kerja_audit,id',
            'planned_walkthrough_date' => 'required|date',
            'actual_walkthrough_date'  => 'nullable|date',
            'auditee_id'               => 'required|exists:master_auditee,id',
            'hasil_walkthrough'        => 'required|string',
            'file_bpm'                 => 'nullable|file|mimes:pdf|max:5120',
        ];
    }
}
