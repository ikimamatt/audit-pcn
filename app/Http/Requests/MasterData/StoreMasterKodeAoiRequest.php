<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class StoreMasterKodeAoiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'indikator_pengawasan'          => 'required|string|max:255',
            'kode_area_of_improvement'      => 'required|string|max:255|unique:master_kode_aoi',
            'deskripsi_area_of_improvement' => 'required|string',
        ];
    }
}
