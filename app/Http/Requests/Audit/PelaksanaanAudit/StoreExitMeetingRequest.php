<?php

namespace App\Http\Requests\Audit\PelaksanaanAudit;

use Illuminate\Foundation\Http\FormRequest;

class StoreExitMeetingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'perencanaan_audit_id' => 'required|exists:perencanaan_audit,id',
            'tanggal_mulai'        => 'nullable|date',
            'tanggal_selesai'      => 'nullable|date|after_or_equal:tanggal_mulai',
            'file_undangan'        => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'file_absensi'         => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
        ];
    }
}
