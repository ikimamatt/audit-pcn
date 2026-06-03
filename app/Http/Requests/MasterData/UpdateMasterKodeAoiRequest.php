<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMasterKodeAoiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $aoiId = $this->route('masterKodeAoi') ? $this->route('masterKodeAoi')->id : null;

        return [
            'indikator_pengawasan'          => 'required|string|max:255',
            'kode_area_of_improvement'      => 'required|string|max:255|unique:master_kode_aoi,kode_area_of_improvement,' . $aoiId,
            'deskripsi_area_of_improvement' => 'required|string',
        ];
    }
}
