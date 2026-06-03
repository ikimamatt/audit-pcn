<?php

namespace App\Http\Requests\Audit\PelaporanAudit;

use Illuminate\Foundation\Http\FormRequest;

class ApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => 'required|string|in:approve,reject',
            'rejection_reason' => 'required_if:action,reject|nullable|string|min:10',
        ];
    }

    public function messages(): array
    {
        return [
            'action.required' => 'Aksi persetujuan harus dipilih.',
            'rejection_reason.required_if' => 'Alasan penolakan harus diisi jika Anda menolak.',
            'rejection_reason.min' => 'Alasan penolakan minimal 10 karakter.',
        ];
    }
}
