<?php

namespace App\Http\Requests\Audit\TindakLanjut;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTindakLanjutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'real_waktu'           => 'nullable|date',
            'komentar'             => 'nullable|string',
            'file_eviden'          => 'nullable|file|max:2048',
            'status_tindak_lanjut' => 'required|in:open,closed,on_progress',
        ];
    }
}
