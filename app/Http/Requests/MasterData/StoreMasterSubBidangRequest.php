<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class StoreMasterSubBidangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama'             => 'required|string|max:255',
            'master_bidang_id' => 'required|exists:master_auditee,id',
        ];
    }
}
