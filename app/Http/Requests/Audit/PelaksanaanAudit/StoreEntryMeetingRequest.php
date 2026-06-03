<?php

namespace App\Http\Requests\Audit\PelaksanaanAudit;

use Illuminate\Foundation\Http\FormRequest;

class StoreEntryMeetingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'program_kerja_audit_id' => 'required|exists:program_kerja_audit,id',
            'planned_meeting_date'   => 'required|date',
            'actual_meeting_date'    => 'nullable|date',
            'auditee_id'             => 'required|exists:master_auditee,id',
            'file_undangan'          => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'file_absensi'           => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }
}
