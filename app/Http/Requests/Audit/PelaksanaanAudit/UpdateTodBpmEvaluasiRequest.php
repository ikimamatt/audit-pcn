<?php

namespace App\Http\Requests\Audit\PelaksanaanAudit;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTodBpmEvaluasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hasil_evaluasi' => 'required|string',
        ];
    }
}
