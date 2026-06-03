<?php

namespace App\Http\Requests\Audit\PelaksanaanAudit;

use Illuminate\Foundation\Http\FormRequest;

class StoreTodBpmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'perencanaan_audit_id' => 'required|exists:perencanaan_audit,id',
            'judul_bpm'            => 'required|string',
            'nama_bpo'             => 'required|string',
            'walkthrough_id'       => 'required|exists:walkthrough_audit,id',
            'pka_risiko_ids'       => 'nullable|array',
            'pka_risiko_ids.*'     => 'exists:pka_risiko,id',
            'pka_kontrol_ids'      => 'nullable|array',
            'pka_kontrol_ids.*'    => 'exists:pka_kontrol,id',
            'file_kka_tod'         => 'nullable|file|mimes:pdf|max:5120',
            'hasil_evaluasi'       => 'required|string|in:Cukup,Tidak Cukup',
        ];
    }
}
