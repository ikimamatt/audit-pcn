<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class StoreMasterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama'                 => 'required|string|max:255',
            'username'             => 'required|string|max:255|unique:master_user,username',
            'nip'                  => 'required|string|max:255',
            'password'             => 'required|string|min:6',
            'email'                => 'nullable|email|max:255',
            'no_telpon'            => 'nullable|string|max:20',
            'jabatan'              => 'nullable|string|max:255',
            'master_auditee_id'    => 'required|exists:master_auditee,id',
            'master_area_id'       => 'required|exists:master_area,id',
            'master_akses_user_id' => 'required|exists:master_akses_user,id',
        ];
    }
}
