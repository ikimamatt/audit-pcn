<?php

namespace App\Http\Requests\Audit\PerencanaanAudit;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePerencanaanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal_surat_tugas'  => 'required|date',
            'nomor_surat_tugas'    => 'required|string|max:255',
            'jenis_audit_id'       => 'required|exists:master_jenis_audit,id',
            'area_id'              => 'nullable|exists:master_area,id',
            'auditor'              => 'nullable|array',
            'auditor.*'            => 'nullable|exists:master_user,id',
            'auditee'              => 'required|exists:master_auditee,id',
            'ruang_lingkup'        => 'required|array',
            'tanggal_audit_mulai'  => 'required|date',
            'tanggal_audit_sampai' => 'required|date',
            'periode_awal'         => 'required',
            'periode_akhir'        => 'required',
            'koordinator_id'       => 'nullable|exists:master_user,id',
            'ketua_tim_id'         => 'nullable|exists:master_user,id',
        ];
    }
}
