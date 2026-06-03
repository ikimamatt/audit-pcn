<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMasterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('masterUser') ? $this->route('masterUser')->id : null;

        return [
            'nama'                 => 'required|string|max:255',
            'username'             => 'required|string|max:255|unique:master_user,username,' . $userId,
            'nip'                  => 'required|string|max:255',
            'email'                => 'nullable|email|max:255',
            'no_telpon'            => 'nullable|string|max:20',
            'jabatan'              => 'nullable|string|max:255',
            'master_auditee_id'    => 'required|exists:master_auditee,id',
            'master_area_id'       => 'required|exists:master_area,id',
            'master_akses_user_id' => 'required|exists:master_akses_user,id',
            'password'             => 'nullable|string|min:6',
        ];
    }
}
