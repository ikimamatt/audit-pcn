<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class AuthHelper
{
    // =========================================================
    // ROLE CHECKERS — 6 Role Baku
    // =========================================================

    public static function isKspi(): bool
    {
        return self::hasRole('KSPI');
    }

    public static function isAsmanSpi(): bool
    {
        return self::hasRole('ASMAN SPI');
    }

    /** @deprecated Gunakan isAsmanSpi() */
    public static function isAsmanKspi(): bool
    {
        return self::isAsmanSpi();
    }

    public static function isAuditor(): bool
    {
        return self::hasRole('AUDITOR');
    }

    public static function isAuditee(): bool
    {
        return self::hasRole('AUDITEE');
    }

    public static function isSuperAdmin(): bool
    {
        return self::hasRole('SUPER ADMIN');
    }



    public static function isViewBod(): bool
    {
        return self::hasRole('VIEW BOD');
    }

    /** @deprecated Gunakan isViewBod() */
    public static function isBod(): bool
    {
        return self::isViewBod();
    }

    /**
     * Shortcut: apakah user adalah bagian dari tim SPI (bukan AUDITEE atau VIEW BOD)
     */
    public static function isSpiTeam(): bool
    {
        return self::isKspi() || self::isAsmanSpi() || self::isAuditor();
    }

    // =========================================================
    // DATA ACCESS
    // =========================================================

    /**
     * Apakah user bisa melihat semua data (bukan AUDITEE)
     * AUDITEE hanya melihat data unit/divisi mereka sendiri
     */
    public static function canSeeAllData(): bool
    {
        if (!Auth::check()) return false;
        return !self::isAuditee();
    }

    /**
     * Apakah user bisa membuat, mengedit, atau menghapus data
     * AUDITEE dan VIEW BOD tidak bisa memodifikasi data
     */
    public static function canModifyData(): bool
    {
        if (!Auth::check()) return false;
        return !self::isAuditee() && !self::isViewBod();
    }

    /**
     * Dapatkan auditee_id user yang sedang login
     * Null = bisa melihat semua data (non-AUDITEE)
     * @deprecated Gunakan getUserAreaId() untuk filter berbasis unit (area)
     */
    public static function getUserAuditeeId(): ?int
    {
        if (!Auth::check()) return null;
        if (!self::isAuditee()) return null;

        $user = Auth::user();
        return $user->master_auditee_id ?? null;
    }

    /**
     * Dapatkan area_id (unit) user yang sedang login.
     * Digunakan untuk memfilter data auditee berdasarkan unit mereka.
     * Null jika bukan AUDITEE atau belum login.
     */
    public static function getUserAreaId(): ?int
    {
        if (!Auth::check()) return null;
        if (!self::isAuditee()) return null;

        $user = Auth::user();
        return $user->master_area_id ?? null;
    }

    /**
     * Dapatkan role name user yang sedang login
     */
    public static function getRole(): ?string
    {
        if (!Auth::check()) return null;

        $user = Auth::user();
        if (!$user->relationLoaded('akses')) {
            $user->load('akses');
        }

        return $user->akses?->nama_akses;
    }

    /**
     * Dapatkan ID user yang sedang login
     */
    public static function getCurrentUserId(): ?int
    {
        return Auth::id();
    }

    // =========================================================
    // APPROVAL SHORTCUTS (Delegasi ke ApprovalHelper)
    // =========================================================

    /**
     * Apakah user saat ini bisa approve/reject APAPUN (SUPER ADMIN atau ketua/koordinator)
     * @deprecated Gunakan ApprovalHelper::canApproveLevel1() atau canApproveLevel2()
     */
    public static function canApproveReject(): bool
    {
        return self::isSuperAdmin() || self::isSpiTeam();
    }

    /** @deprecated */
    public static function hasAsmanSpiUsers(): bool { return true; }
    /** @deprecated */
    public static function hasAsmanKspiUser(): bool { return true; }
    /** @deprecated */
    public static function hasAsmanKspiUsers(): bool { return true; }
    /** @deprecated */
    public static function isPic(): bool { return self::isAuditee(); }
    


    // =========================================================
    // INTERNAL
    // =========================================================

    private static function hasRole(string $role): bool
    {
        if (!Auth::check()) return false;

        $user = Auth::user();
        if (!$user->relationLoaded('akses')) {
            $user->load('akses');
        }

        return $user->akses?->nama_akses === $role;
    }
}
