<?php

namespace App\Http\Requests\Audit\PelaporanAudit;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePenutupLhaRekomendasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pelaporan_isi_lha_id' => 'required|exists:pelaporan_temuan,id',
            'rekomendasi'          => 'required|string|max:5000',
            'rencana_aksi'         => 'required|string|max:5000',
            'eviden_rekomendasi'   => 'required|string|max:5000',
            'pic_business_contact' => 'required|exists:master_user,id',
            'pic_approval_1_spi'   => 'required|exists:master_user,id',
            'pic_approval_2_spi'   => 'required|exists:master_user,id',
            'target_waktu'         => 'required|date',
        ];
    }
}
