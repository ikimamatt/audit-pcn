<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

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
                'realisasi_audits',
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
     * Cek apakah user saat ini bisa approve Level 1 (Ketua Tim atau PIC approval_1_spi)
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

        if ($item->getTable() === 'penutup_lha_rekomendasi') {
            return DB::table('penutup_lha_rekomendasi_pic')
                ->where('penutup_lha_rekomendasi_id', $item->id)
                ->where('master_user_id', $userId)
                ->where('pic_type', 'approval_1_spi')
                ->exists() && $validStatusLvl1;
        }

        $perencanaan = self::getPerencanaanAudit($item);
        if (!$perencanaan) return false;

        return (int) $perencanaan->ketua_tim_id === $userId && $validStatusLvl1;
    }

    /**
     * Cek apakah user saat ini bisa approve Level 2 (Koordinator atau PIC approval_2_spi)
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

        if ($item->getTable() === 'penutup_lha_rekomendasi') {
            return DB::table('penutup_lha_rekomendasi_pic')
                ->where('penutup_lha_rekomendasi_id', $item->id)
                ->where('master_user_id', $userId)
                ->where('pic_type', 'approval_2_spi')
                ->exists() && $validStatusLvl2;
        }

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

        if ($item->getTable() === 'penutup_lha_rekomendasi') {
            $isPic1 = DB::table('penutup_lha_rekomendasi_pic')
                ->where('penutup_lha_rekomendasi_id', $item->id)
                ->where('master_user_id', $userId)
                ->where('pic_type', 'approval_1_spi')
                ->exists();
            $isPic2 = DB::table('penutup_lha_rekomendasi_pic')
                ->where('penutup_lha_rekomendasi_id', $item->id)
                ->where('master_user_id', $userId)
                ->where('pic_type', 'approval_2_spi')
                ->exists();

            if ($isPic1 && $validKetuaReject) return true;
            if ($isPic2 && $validKoordReject) return true;
            return false;
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
        $tableName = $item->getTable();
        $itemId    = $item->getKey();

        if (!$perencanaan && !$isSuperAdmin && $tableName !== 'penutup_lha_rekomendasi') {
            return ['success' => false, 'message' => 'Data perencanaan audit tidak ditemukan.'];
        }

        $isKetua = false;
        $isKoordinator = false;

        if ($tableName === 'penutup_lha_rekomendasi') {
            $isKetua = DB::table('penutup_lha_rekomendasi_pic')
                ->where('penutup_lha_rekomendasi_id', $itemId)
                ->where('master_user_id', $userId)
                ->where('pic_type', 'approval_1_spi')
                ->exists();
            $isKoordinator = DB::table('penutup_lha_rekomendasi_pic')
                ->where('penutup_lha_rekomendasi_id', $itemId)
                ->where('master_user_id', $userId)
                ->where('pic_type', 'approval_2_spi')
                ->exists();
        } else {
            $ketuaId      = $perencanaan ? (int) $perencanaan->ketua_tim_id : null;
            $koordinatorId = $perencanaan ? (int) $perencanaan->koordinator_id : null;

            $isKetua      = $ketuaId && $ketuaId === $userId;
            $isKoordinator = $koordinatorId && $koordinatorId === $userId;
        }

        $status    = $item->status_approval ?? 'pending';

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
                $updateData = [
                    'status_approval'    => 'approved_level1',
                    'approved_by_level1' => $userId,
                    'approved_at_level1' => now(),
                ];

                if ($tableName === 'penutup_lha_rekomendasi') {
                    $updateData['status_tindak_lanjut'] = 'on_progress';
                }

                $updated = DB::table($tableName)->where('id', $itemId)->update($updateData);

                if ($updated === false) {
                    return ['success' => false, 'message' => 'Gagal menyimpan approval Level 1.'];
                }

                $item->refresh();
                
                // Juga update status tindak lanjut terbaru di tabel riwayat jika ada
                if ($tableName === 'penutup_lha_rekomendasi') {
                    $latestTl = $item->tindakLanjut()->orderBy('created_at', 'desc')->first();
                    if ($latestTl) {
                        $latestTl->update(['status_tindak_lanjut' => 'on_progress']);
                    }
                }

                return ['success' => true, 'message' => 'Berhasil diapprove Level 1 (Ketua Tim / PIC 1).'];
            }

            // Level 2: Koordinator (atau SUPER ADMIN dari approved_level1)
            if (($isKoordinator || $isSuperAdmin) && $status === 'approved_level1') {
                $updateData = [
                    'status_approval'    => 'approved',
                    'approved_by_level2' => $userId,
                    'approved_at_level2' => now(),
                ];

                if (Schema::hasColumn($tableName, 'approved_by')) {
                    $updateData['approved_by'] = $userId;
                }
                if (Schema::hasColumn($tableName, 'approved_at')) {
                    $updateData['approved_at'] = now();
                }
                if ($tableName === 'penutup_lha_rekomendasi') {
                    $updateData['status_tindak_lanjut'] = 'closed';
                }

                $updated = DB::table($tableName)->where('id', $itemId)->update($updateData);

                if ($updated === false) {
                    return ['success' => false, 'message' => 'Gagal menyimpan approval Level 2.'];
                }

                $item->refresh();

                // Juga update status tindak lanjut terbaru di tabel riwayat jika ada
                if ($tableName === 'penutup_lha_rekomendasi') {
                    $latestTl = $item->tindakLanjut()->orderBy('created_at', 'desc')->first();
                    if ($latestTl) {
                        $latestTl->update(['status_tindak_lanjut' => 'closed']);
                    }
                }

                return ['success' => true, 'message' => 'Berhasil diapprove Level 2 (Koordinator / PIC 2). Dokumen selesai diapprove!'];
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

            // Ketua Tim / PIC 1 reject dari pending
            if (($isKetua || $isSuperAdmin) && in_array($status, ['pending', 'rejected_level1'])) {
                $updateData = [
                    'status_approval'         => 'rejected_level1',
                    'rejected_by_level1'      => $userId,
                    'rejected_at_level1'      => now(),
                    'rejection_reason_level1' => $reason,
                ];

                if ($tableName === 'penutup_lha_rekomendasi') {
                    $updateData['status_tindak_lanjut'] = 'open';
                }

                DB::table($tableName)->where('id', $itemId)->update($updateData);

                $item->refresh();

                // Juga update status tindak lanjut terbaru di tabel riwayat jika ada
                if ($tableName === 'penutup_lha_rekomendasi') {
                    $latestTl = $item->tindakLanjut()->orderBy('created_at', 'desc')->first();
                    if ($latestTl) {
                        $latestTl->update(['status_tindak_lanjut' => 'open']);
                    }
                }

                return ['success' => true, 'message' => 'Dokumen ditolak Level 1. Alasan: ' . $reason];
            }

            // Koordinator / PIC 2 reject dari approved_level1
            if (($isKoordinator || $isSuperAdmin) && $status === 'approved_level1') {
                $updateData = [
                    'status_approval'         => 'rejected',
                    'rejected_by_level2'      => $userId,
                    'rejected_at_level2'      => now(),
                    'rejection_reason_level2' => $reason,
                ];

                if ($tableName === 'penutup_lha_rekomendasi') {
                    $updateData['status_tindak_lanjut'] = 'open';
                }

                DB::table($tableName)->where('id', $itemId)->update($updateData);

                $item->refresh();

                // Juga update status tindak lanjut terbaru di tabel riwayat jika ada
                if ($tableName === 'penutup_lha_rekomendasi') {
                    $latestTl = $item->tindakLanjut()->orderBy('created_at', 'desc')->first();
                    if ($latestTl) {
                        $latestTl->update(['status_tindak_lanjut' => 'open']);
                    }
                }

                return ['success' => true, 'message' => 'Dokumen ditolak Level 2. Alasan: ' . $reason];
            }

            return [
                'success' => false,
                'message' => 'Anda tidak berwenang melakukan penolakan ini, atau status dokumen tidak sesuai. Status: ' . $status,
            ];
        }

        return ['success' => false, 'message' => 'Aksi tidak valid.'];
    }
}
