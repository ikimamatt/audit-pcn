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
            'proses_bisnis'          => 'required|array|min:1',
            'proses_bisnis.*.nama'   => 'required|string',
            'proses_bisnis.*.risiko' => 'nullable|array',
            'informasi_umum'         => 'nullable|string',
            'kpi_tidak_tercapai'     => 'nullable|string',
            'data_awal_dokumen'      => 'nullable|array',
            'data_awal_dokumen.*.nama_dokumen'  => 'nullable|string',
            'data_awal_dokumen.*.ruang_lingkup' => 'nullable|string',
            'data_awal_dokumen.*.periode'       => 'nullable|string',
            'milestone'              => 'nullable|array',
            'milestone.*'            => 'nullable|array',
            'milestone.*.mulai'      => 'nullable|date',
            'milestone.*.selesai'    => 'nullable|date',
            'dokumen.*'              => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
        ];
    }
}
