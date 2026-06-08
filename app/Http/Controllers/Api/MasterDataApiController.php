<?php

namespace App\Http\Controllers\Api;

use App\Models\MasterData\MasterAuditee;
use App\Models\MasterData\MasterArea;
use App\Models\MasterData\MasterJenisAudit;
use App\Models\MasterData\MasterKodeAoi;
use App\Models\MasterData\MasterKodeRisk;
use App\Models\MasterData\MasterUser;
use App\Models\MasterData\MasterUnit;
use App\Models\MasterData\MasterRegion;
use App\Models\MasterData\MasterSubBidang;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MasterDataApiController extends BaseApiController
{
    /**
     * Daftar master auditee.
     */
    public function auditee(): JsonResponse
    {
        return $this->success(MasterAuditee::all());
    }

    /**
     * Daftar master area (unit layanan).
     */
    public function area(): JsonResponse
    {
        return $this->success(MasterArea::with('region')->orderBy('kd_area')->get());
    }

    /**
     * Daftar master jenis audit.
     */
    public function jenisAudit(): JsonResponse
    {
        return $this->success(MasterJenisAudit::all());
    }

    /**
     * Daftar master kode risk.
     */
    public function kodeRisk(): JsonResponse
    {
        return $this->success(MasterKodeRisk::all());
    }

    /**
     * Daftar master kode AOI.
     */
    public function kodeAoi(): JsonResponse
    {
        return $this->success(MasterKodeAoi::all());
    }

    /**
     * Daftar master user (non-password fields).
     */
    public function user(): JsonResponse
    {
        $users = MasterUser::with('akses')
            ->select('id', 'nama', 'nip', 'email', 'jabatan', 'master_akses_user_id', 'master_auditee_id', 'master_area_id')
            ->orderBy('nama')
            ->get();

        return $this->success($users);
    }

    /**
     * Daftar master region (unit pelaksana).
     */
    public function region(): JsonResponse
    {
        return $this->success(MasterRegion::all());
    }

    /**
     * Daftar master sub bidang.
     */
    public function subBidang(): JsonResponse
    {
        return $this->success(MasterSubBidang::all());
    }
}
