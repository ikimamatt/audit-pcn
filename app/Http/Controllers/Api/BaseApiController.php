<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MasterData\MasterUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Base API Controller untuk integrasi ERP.
 *
 * Menyediakan helper methods untuk mengakses data user ERP
 * yang diinject oleh middleware ValidationERPToken.
 */
abstract class BaseApiController extends Controller
{
    /**
     * Mendapatkan semua data user ERP dari request.
     */
    protected function erpUser(Request $request): array
    {
        return $request->input('_erp_user', []);
    }

    /**
     * Mendapatkan user lokal Audit PCN (jika NIP ditemukan).
     */
    protected function localUser(Request $request): ?MasterUser
    {
        return $request->attributes->get('erp_local_user');
    }

    /**
     * Mendapatkan role lokal user di Audit PCN.
     */
    protected function localRole(Request $request): string
    {
        return $request->attributes->get('erp_local_role', 'VIEW_ONLY');
    }

    /**
     * Cek apakah user bisa melakukan modifikasi data (create, update, delete).
     */
    protected function canModify(Request $request): bool
    {
        return (bool) $request->attributes->get('erp_can_modify', false);
    }

    /**
     * Cek apakah user hanya bisa melihat data (view only).
     */
    protected function isViewOnly(Request $request): bool
    {
        return $this->localRole($request) === 'VIEW_ONLY';
    }

    /**
     * Cek apakah user lokal memiliki salah satu role yang diizinkan.
     */
    protected function hasRole(Request $request, string ...$roles): bool
    {
        return in_array($this->localRole($request), $roles);
    }

    /**
     * Response 403 jika user tidak bisa modify.
     */
    protected function denyModify(): JsonResponse
    {
        return $this->error('Anda tidak memiliki akses untuk melakukan aksi ini.', 403);
    }

    /**
     * Standar success response.
     */
    protected function success(mixed $data = null, string $message = 'Berhasil.', int $code = 200, array $meta = []): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ];

        if (! empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $code);
    }

    /**
     * Standar error response.
     */
    protected function error(string $message = 'Terjadi kesalahan.', int $code = 400, mixed $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'code'    => $code,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }
}
