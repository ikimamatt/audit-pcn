<?php

namespace App\Http\Requests\Audit\TindakLanjut;

use Illuminate\Foundation\Http\FormRequest;

class StoreTindakLanjutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'real_waktu'    => 'nullable|date',
            'komentar'      => 'required|array|min:1',
            'komentar.*'    => 'required|string|min:3',
            'file_eviden'   => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'file_eviden.required' => 'File eviden wajib diupload.',
            'file_eviden.file'     => 'File eviden tidak valid.',
            'file_eviden.mimes'    => 'Format file eviden harus berupa: PDF, JPG, PNG, DOC, atau DOCX.',
            'file_eviden.max'      => 'Ukuran file eviden maksimal 5MB.',
        ];
    }
}
