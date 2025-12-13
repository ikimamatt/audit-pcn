<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthHelper
{
    /**
     * Check if current user has access to approve/reject
     * Only ASMAN KSPI and KSPI can approve/reject
     */
    public static function canApproveReject(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        
        // Load akses relationship if not loaded
        if (!$user->relationLoaded('akses')) {
            $user->load('akses');
        }

        if (!$user->akses) {
            return false;
        }

        $namaAkses = $user->akses->nama_akses;
        
        return in_array($namaAkses, ['ASMAN KSPI', 'KSPI']);
    }

    /**
     * Check if current user is ASMAN KSPI
     */
    public static function isAsmanKspi(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        
        if (!$user->relationLoaded('akses')) {
            $user->load('akses');
        }

        if (!$user->akses) {
            return false;
        }

        return $user->akses->nama_akses === 'ASMAN KSPI';
    }

    /**
     * Check if current user is KSPI
     */
    public static function isKspi(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        
        if (!$user->relationLoaded('akses')) {
            $user->load('akses');
        }

        if (!$user->akses) {
            return false;
        }

        return $user->akses->nama_akses === 'KSPI';
    }

    /**
     * Check if current user can approve at level 1 (ASMAN KSPI)
     * Can approve if status is 'pending'
     */
    public static function canApproveLevel1($item): bool
    {
        if (!self::isAsmanKspi()) {
            return false;
        }

        return $item->status_approval === 'pending';
    }

    /**
     * Check if current user can approve at level 2 (KSPI)
     * Can approve if status is 'approved_level1' or 'pending' (if no ASMAN KSPI user exists)
     */
    public static function canApproveLevel2($item): bool
    {
        if (!self::isKspi()) {
            return false;
        }

        // Jika tidak ada user ASMAN KSPI, KSPI bisa langsung approve dari pending
        if (!self::hasAsmanKspiUsers()) {
            return $item->status_approval === 'pending';
        }

        // Jika ada user ASMAN KSPI, harus menunggu approval level 1
        return $item->status_approval === 'approved_level1';
    }

    /**
     * Check if current user can reject at level 1 (ASMAN KSPI)
     * Can reject if status is 'pending'
     */
    public static function canRejectLevel1($item): bool
    {
        if (!self::isAsmanKspi()) {
            return false;
        }

        return $item->status_approval === 'pending';
    }

    /**
     * Check if current user can reject at level 2 (KSPI)
     * Can reject if status is 'pending', 'approved_level1', or 'rejected_level1' (berjenjang)
     */
    public static function canRejectLevel2($item): bool
    {
        if (!self::isKspi()) {
            return false;
        }

        return in_array($item->status_approval, ['pending', 'approved_level1', 'rejected_level1']);
    }

    /**
     * Check if there are any users with ASMAN KSPI access in the database
     * 
     * @return bool
     */
    public static function hasAsmanKspiUser(): bool
    {
        $asmanKspiAkses = DB::table('master_akses_user')
            ->where('nama_akses', 'ASMAN KSPI')
            ->first();

        if (!$asmanKspiAkses) {
            return false;
        }

        $asmanKspiCount = DB::table('master_user')
            ->where('master_akses_user_id', $asmanKspiAkses->id)
            ->count();

        return $asmanKspiCount > 0;
    }

    /**
     * Check if there are any users with ASMAN KSPI access in the database
     * Alias for hasAsmanKspiUser() for consistency
     * 
     * @return bool
     */
    public static function hasAsmanKspiUsers(): bool
    {
        // Cache the result to avoid multiple database queries in a single request
        static $hasAsmanKspi = null;

        if ($hasAsmanKspi === null) {
            $asmanKspiAksesId = DB::table('master_akses_user')
                                ->where('nama_akses', 'ASMAN KSPI')
                                ->value('id');

            if ($asmanKspiAksesId) {
                $hasAsmanKspi = DB::table('master_user')
                                ->where('master_akses_user_id', $asmanKspiAksesId)
                                ->exists();
            } else {
                $hasAsmanKspi = false;
            }
        }
        return $hasAsmanKspi;
    }

    /**
     * Get current user's auditee_id (divisi/cabang)
     * Returns null if user doesn't have auditee or if user has special access
     * 
     * @return int|null
     */
    public static function getUserAuditeeId(): ?int
    {
        if (!Auth::check()) {
            return null;
        }

        $user = Auth::user();
        
        // Load akses relationship if not loaded
        if (!$user->relationLoaded('akses')) {
            $user->load('akses');
        }

        if (!$user->akses) {
            return null;
        }

        $namaAkses = $user->akses->nama_akses;
        
        // User dengan akses KSPI, ASMAN KSPI, atau Auditor bisa melihat semua data
        if (in_array($namaAkses, ['KSPI', 'ASMAN KSPI', 'Auditor'])) {
            return null; // null berarti bisa melihat semua
        }

        // Load auditee relationship if not loaded
        if (!$user->relationLoaded('auditee')) {
            $user->load('auditee');
        }

        return $user->master_auditee_id ?? null;
    }

    /**
     * Check if current user can see all data (KSPI, ASMAN KSPI, Auditor)
     * 
     * @return bool
     */
    public static function canSeeAllData(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        
        if (!$user->relationLoaded('akses')) {
            $user->load('akses');
        }

        if (!$user->akses) {
            return false;
        }

        $namaAkses = $user->akses->nama_akses;
        
        return in_array($namaAkses, ['KSPI', 'ASMAN KSPI', 'Auditor']);
    }

    /**
     * Check if current user is BOD
     * 
     * @return bool
     */
    public static function isBod(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        
        if (!$user->relationLoaded('akses')) {
            $user->load('akses');
        }

        if (!$user->akses) {
            return false;
        }

        return $user->akses->nama_akses === 'BOD';
    }

    /**
     * Check if current user can create, edit, or delete data
     * BOD cannot create, edit, or delete
     * 
     * @return bool
     */
    public static function canModifyData(): bool
    {
        return !self::isBod();
    }
}

