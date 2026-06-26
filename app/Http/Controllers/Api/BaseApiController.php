<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MasterData\MasterUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Base API Controller untuk integrasi ERP.
 *
 * Menyediakan helper methods untuk mengakses data user ERP
 * yang diinject oleh middleware ValidationERPToken,
 * serta helper pagination berbasis Stored Procedure.
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
     * Standar paginated success response.
     * Wrapper sukses khusus untuk data list yang sudah terpaginasi.
     */
    protected function successPaginated(
        mixed  $data,
        int    $total,
        int    $page,
        int    $perPage,
        string $message = 'Berhasil.'
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
            'meta'    => [
                'total'     => $total,
                'page'      => $page,
                'per_page'  => $perPage,
                'last_page' => $total > 0 ? (int) ceil($total / $perPage) : 1,
                'from'      => $total > 0 ? (($page - 1) * $perPage) + 1 : 0,
                'to'        => min($page * $perPage, $total),
            ],
        ]);
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

    /**
     * Helper: resolve parameter pagination dari request.
     * Mengembalikan [perPage, page, offset].
     *
     * @param  int  $defaultPerPage  Default jumlah item per halaman (15)
     * @param  int  $maxPerPage      Batas maksimal per halaman untuk mencegah abuse (100)
     */
    protected function resolvePagination(Request $request, int $defaultPerPage = 15, int $maxPerPage = 100): array
    {
        $perPage = min((int) $request->input('limit', $defaultPerPage), $maxPerPage);
        $perPage = max($perPage, 1);

        $page   = max((int) $request->input('page', 1), 1);
        $offset = ($page - 1) * $perPage;

        return [$perPage, $page, $offset];
    }

    /**
     * Jalankan Stored Procedure dan kembalikan [total, rows].
     *
     * SP diasumsikan mengembalikan 2 result set:
     *   1) SELECT COUNT(*) AS total_count  (untuk total keseluruhan)
     *   2) SELECT ... LIMIT p_limit OFFSET p_offset  (untuk data halaman ini)
     *
     * @param  string  $spName  Nama stored procedure, e.g. 'sp_get_perencanaan_audit'
     * @param  array   $params  Array parameter positional untuk SP
     * @return array            [$total (int), $rows (array)]
     */
    protected function callSP(string $spName, array $params = []): array
    {
        $placeholders = implode(', ', array_fill(0, count($params), '?'));
        $sql  = "CALL `{$spName}`({$placeholders})";

        $pdo  = DB::getPdo();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Result set 1: total count
        $countRow = $stmt->fetch(\PDO::FETCH_ASSOC);
        $total    = (int) ($countRow['total_count'] ?? 0);

        // Result set 2: paginated rows
        $stmt->nextRowset();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return [$total, $rows];
    }
}
