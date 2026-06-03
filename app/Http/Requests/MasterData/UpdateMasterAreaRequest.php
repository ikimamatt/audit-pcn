<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMasterAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $areaId = $this->route('masterArea') ? $this->route('masterArea')->id : null;

        return [
            'kd_area'   => 'required|string|max:50|unique:master_area,kd_area,' . $areaId,
            'nama_area' => 'required|string|max:255',
            'kd_region' => 'nullable|string|max:50|exists:master_region,kd_region',
        ];
    }
}
