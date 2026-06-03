<?php

namespace App\Http\Requests\Audit\PerencanaanAudit;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJadwalPkptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'auditee_id'      => 'required|exists:master_auditee,id',
            'jenis_audit'     => 'required',
            'jumlah_auditor'  => 'required|integer|min:1',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
        ];
    }
}
