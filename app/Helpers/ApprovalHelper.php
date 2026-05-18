<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalHelper
{
    /**
     * Dapatkan data perencanaan_audit dari model apapun (trace melalui relasi).
     * Mengembalikan object perencanaan_audit atau null.
     */
    public static function getPerencanaanAudit($item): ?object
    {
        $table = $item->getTable();

        try {
            // Tabel dengan perencanaan_audit_id langsung
            $directTables = [
                'program_kerja_audit',
                'walkthrough_audit',
                'tod_bpm_audit',
                'toe_audit',
                'pelaporan_hasil_audit',
            ];

            if (in_array($table, $directTables)) {
                return DB::table('perencanaan_audit')
                    ->where('id', $item->perencanaan_audit_id)
                    ->first();
            }

            // entry_meeting & pka_dokumen → melalui program_kerja_audit
            if (in_array($table, ['entry_meeting', 'pka_dokumen'])) {
                $pka = DB::table('program_kerja_audit')
                    ->where('id', $item->program_kerja_audit_id)
                    ->first();

                if (!$pka) return null;

                return DB::table('perencanaan_audit')
                    ->where('id', $pka->perencanaan_audit_id)
                    ->first();
            }

            // penutup_lha_rekomendasi → temuan → pelaporan_hasil_audit → perencanaan_audit
            if ($table === 'penutup_lha_rekomendasi') {
                $temuan = DB::table('pelaporan_temuan')
                    ->where('id', $item->pelaporan_isi_lha_id ?? $item->pelaporan_temuan_id ?? $item->temuan_id ?? null)
                    ->first();

                if (!$temuan) return null;

                $pelaporan = DB::table('pelaporan_hasil_audit')
                    ->where('id', $temuan->pelaporan_hasil_audit_id)
                    ->first();

                if (!$pelaporan) return null;

                return DB::table('perencanaan_audit')
                    ->where('id', $pelaporan->perencanaan_audit_id)
                    ->first();
            }

        } catch (\Exception $e) {
            Log::error('ApprovalHelper::getPerencanaanAudit error', [
                'table' => $table,
                'id'    => $item->getKey(),
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Cek apakah user saat ini bisa approve Level 1 (Ketua Tim)
     * SUPER ADMIN selalu bisa.
     */
    public static function canApproveLevel1($item, ?int $userId = null): bool
    {
        if (!Auth::check()) return false;

        $userId = $userId ?? Auth::id();

        $status = $item->status_approval ?? 'pending';
        $validStatusLvl1 = in_array($status, ['pending', 'rejected_level1']);

        // SUPER ADMIN bypass role, but still respect the status workflow
        if (AuthHelper::isSuperAdmin()) return $validStatusLvl1;

        $perencanaan = self::getPerencanaanAudit($item);
        if (!$perencanaan) return false;

        return (int) $perencanaan->ketua_tim_id === $userId && $validStatusLvl1;
    }

    /**
     * Cek apakah user saat ini bisa approve Level 2 (Koordinator)
     * SUPER ADMIN selalu bisa.
     */
    public static function canApproveLevel2($item, ?int $userId = null): bool
    {
        if (!Auth::check()) return false;

        $userId = $userId ?? Auth::id();

        $status = $item->status_approval ?? 'pending';
        $validStatusLvl2 = in_array($status, ['approved_level1', 'rejected']);

        // SUPER ADMIN bypass role, but still respect the status workflow
        if (AuthHelper::isSuperAdmin()) return $validStatusLvl2;

        $perencanaan = self::getPerencanaanAudit($item);
        if (!$perencanaan) return false;

        return (int) $perencanaan->koordinator_id === $userId && $validStatusLvl2;
    }

    /**
     * Cek apakah user saat ini bisa reject (di level manapun yang relevan)
     */
    public static function canReject($item, ?int $userId = null): bool
    {
        if (!Auth::check()) return false;

        $userId = $userId ?? Auth::id();

        $status = $item->status_approval ?? 'pending';
        $validKetuaReject = in_array($status, ['pending', 'rejected_level1']);
        $validKoordReject = $status === 'approved_level1';

        // SUPER ADMIN bypass role
        if (AuthHelper::isSuperAdmin()) {
            return $validKetuaReject || $validKoordReject;
        }

        $perencanaan = self::getPerencanaanAudit($item);
        if (!$perencanaan) return false;

        $isKetua      = (int) $perencanaan->ketua_tim_id === $userId;
        $isKoordinator = (int) $perencanaan->koordinator_id === $userId;

        // Ketua bisa reject dari pending
        if ($isKetua && $validKetuaReject) return true;

        // Koordinator bisa reject dari approved_level1
        if ($isKoordinator && $validKoordReject) return true;

        return false;
    }

    /**
     * Process approval berjenjang berbasis ketua/koordinator
     *
     * @param mixed       $item            Model dokumen
     * @param string      $action          'approve' | 'reject'
     * @param string|null $rejectionReason Wajib diisi jika reject
     * @return array ['success' => bool, 'message' => string]
     */
    public static function processApproval($item, string $action, ?string $rejectionReason = null): array
    {
        if (!Auth::check()) {
            return ['success' => false, 'message' => 'Anda harus login terlebih dahulu.'];
        }

        $userId      = Auth::id();
        $isSuperAdmin = AuthHelper::isSuperAdmin();

        $perencanaan = self::getPerencanaanAudit($item);

        if (!$perencanaan && !$isSuperAdmin) {
            return ['success' => false, 'message' => 'Data perencanaan audit tidak ditemukan.'];
        }

        $ketuaId      = $perencanaan ? (int) $perencanaan->ketua_tim_id : null;
        $koordinatorId = $perencanaan ? (int) $perencanaan->koordinator_id : null;

        $isKetua      = $ketuaId && $ketuaId === $userId;
        $isKoordinator = $koordinatorId && $koordinatorId === $userId;

        $status    = $item->status_approval ?? 'pending';
        $tableName = $item->getTable();
        $itemId    = $item->getKey();

        Log::info('ApprovalHelper::processApproval', [
            'action'         => $action,
            'table'          => $tableName,
            'id'             => $itemId,
            'status'         => $status,
            'userId'         => $userId,
            'isSuperAdmin'   => $isSuperAdmin,
            'isKetua'        => $isKetua,
            'isKoordinator'  => $isKoordinator,
        ]);

        // ==================== APPROVE ====================
        if ($action === 'approve') {

            // Level 1: Ketua Tim (atau SUPER ADMIN dari pending)
            if (($isKetua || $isSuperAdmin) && in_array($status, ['pending', 'rejected_level1'])) {
                $updated = DB::table($tableName)->where('id', $itemId)->update([
                    'status_approval'    => 'approved_level1',
                    'approved_by_level1' => $userId,
                    'approved_at_level1' => now(),
                ]);

                if ($updated === false) {
                    return ['success' => false, 'message' => 'Gagal menyimpan approval Level 1.'];
                }

                $item->refresh();
                return ['success' => true, 'message' => 'Berhasil diapprove Level 1 (Ketua Tim).'];
            }

            // Level 2: Koordinator (atau SUPER ADMIN dari approved_level1)
            if (($isKoordinator || $isSuperAdmin) && $status === 'approved_level1') {
                $updated = DB::table($tableName)->where('id', $itemId)->update([
                    'status_approval'    => 'approved',
                    'approved_by_level2' => $userId,
                    'approved_at_level2' => now(),
                    'approved_by'        => $userId, // backward compat
                    'approved_at'        => now(),   // backward compat
                ]);

                if ($updated === false) {
                    return ['success' => false, 'message' => 'Gagal menyimpan approval Level 2.'];
                }

                $item->refresh();
                return ['success' => true, 'message' => 'Berhasil diapprove Level 2 (Koordinator). Dokumen selesai diapprove!'];
            }

            return [
                'success' => false,
                'message' => 'Anda tidak berwenang melakukan approval ini, atau status dokumen tidak sesuai. Status: ' . $status,
            ];
        }

        // ==================== REJECT ====================
        if ($action === 'reject') {
            if (!$rejectionReason || strlen(trim($rejectionReason)) < 10) {
                return ['success' => false, 'message' => 'Alasan penolakan harus diisi minimal 10 karakter.'];
            }

            $reason = trim($rejectionReason);

            // Ketua Tim reject dari pending
            if (($isKetua || $isSuperAdmin) && in_array($status, ['pending', 'rejected_level1'])) {
                DB::table($tableName)->where('id', $itemId)->update([
                    'status_approval'         => 'rejected_level1',
                    'rejected_by_level1'      => $userId,
                    'rejected_at_level1'      => now(),
                    'rejection_reason_level1' => $reason,
                ]);

                $item->refresh();
                return ['success' => true, 'message' => 'Dokumen ditolak Level 1 (Ketua Tim). Alasan: ' . $reason];
            }

            // Koordinator reject dari approved_level1
            if (($isKoordinator || $isSuperAdmin) && $status === 'approved_level1') {
                DB::table($tableName)->where('id', $itemId)->update([
                    'status_approval'         => 'rejected',
                    'rejected_by_level2'      => $userId,
                    'rejected_at_level2'      => now(),
                    'rejection_reason_level2' => $reason,
                ]);

                $item->refresh();
                return ['success' => true, 'message' => 'Dokumen ditolak Level 2 (Koordinator). Alasan: ' . $reason];
            }

            return [
                'success' => false,
                'message' => 'Anda tidak berwenang melakukan penolakan ini, atau status dokumen tidak sesuai. Status: ' . $status,
            ];
        }

        return ['success' => false, 'message' => 'Aksi tidak valid.'];
    }
}
