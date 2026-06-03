<?php

namespace App\Http\Requests\Audit\TindakLanjut;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePemantauanRekomendasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rekomendasi'        => 'required|string|max:5000',
            'rencana_aksi'       => 'required|string|max:5000',
            'eviden_rekomendasi' => 'required|string|max:5000',
            'pic_rekomendasi'    => 'required|string|max:500',
            'target_waktu'       => 'required|date',
        ];
    }
}
