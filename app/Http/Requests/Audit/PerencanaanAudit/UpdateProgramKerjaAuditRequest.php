<?php

namespace App\Http\Requests\Audit\PerencanaanAudit;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProgramKerjaAuditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'perencanaan_audit_id' => 'required|exists:perencanaan_audit,id',
            'tanggal_pka'          => 'required|date',
            'no_pka'               => 'required',
            'judul_pka'            => 'required|string',
            'proses_bisnis'        => 'required|array|min:1',
            'proses_bisnis.*.nama' => 'required|string',
            'dokumen.*'            => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
        ];
    }
}
