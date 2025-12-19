<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthHelper
{
    /**
     * Check if current user has access to approve/reject
     * Only ASMAN SPI and KSPI can approve/reject
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
        
        return in_array($namaAkses, ['ASMAN SPI', 'KSPI']);
    }

    /**
     * Check if current user is ASMAN SPI
     */
    public static function isAsmanSpi(): bool
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

        return $user->akses->nama_akses === 'ASMAN SPI';
    }

    /**
     * Check if current user is ASMAN KSPI (backward compatibility)
     * @deprecated Use isAsmanSpi() instead
     */
    public static function isAsmanKspi(): bool
    {
        return self::isAsmanSpi();
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
     * Check if current user is AUDITOR
     */
    public static function isAuditor(): bool
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

        // Support both 'Auditor' and 'AUDITOR' for case-insensitive matching
        $namaAkses = $user->akses->nama_akses;
        return in_array($namaAkses, ['Auditor', 'AUDITOR']);
    }

    /**
     * Check if current user can approve at level 1 (ASMAN SPI)
     * Can approve if status is 'pending'
     */
    public static function canApproveLevel1($item): bool
    {
        if (!self::isAsmanSpi()) {
            return false;
        }

        return $item->status_approval === 'pending';
    }

    /**
     * Check if current user can approve at level 2 (KSPI)
     * Can approve if status is 'approved_level1' or 'pending' (if no ASMAN SPI user exists)
     */
    public static function canApproveLevel2($item): bool
    {
        if (!self::isKspi()) {
            return false;
        }

        // Jika tidak ada user ASMAN SPI, KSPI bisa langsung approve dari pending
        if (!self::hasAsmanSpiUsers()) {
            return $item->status_approval === 'pending';
        }

        // Jika ada user ASMAN SPI, harus menunggu approval level 1
        return $item->status_approval === 'approved_level1';
    }

    /**
     * Check if current user can reject at level 1 (ASMAN SPI)
     * Can reject if status is 'pending'
     */
    public static function canRejectLevel1($item): bool
    {
        if (!self::isAsmanSpi()) {
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
     * Check if there are any users with ASMAN SPI access in the database
     * 
     * @return bool
     */
    public static function hasAsmanSpiUsers(): bool
    {
        // Cache the result to avoid multiple database queries in a single request
        static $hasAsmanSpi = null;

        if ($hasAsmanSpi === null) {
            $asmanSpiAksesId = DB::table('master_akses_user')
                                ->where('nama_akses', 'ASMAN SPI')
                                ->value('id');

            if ($asmanSpiAksesId) {
                $hasAsmanSpi = DB::table('master_user')
                                ->where('master_akses_user_id', $asmanSpiAksesId)
                                ->exists();
            } else {
                $hasAsmanSpi = false;
            }
        }
        return $hasAsmanSpi;
    }

    /**
     * Check if there are any users with ASMAN KSPI access in the database (backward compatibility)
     * @deprecated Use hasAsmanSpiUsers() instead
     * 
     * @return bool
     */
    public static function hasAsmanKspiUser(): bool
    {
        return self::hasAsmanSpiUsers();
    }

    /**
     * Check if there are any users with ASMAN KSPI access in the database (backward compatibility)
     * @deprecated Use hasAsmanSpiUsers() instead
     * 
     * @return bool
     */
    public static function hasAsmanKspiUsers(): bool
    {
        return self::hasAsmanSpiUsers();
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
        
        // User dengan akses KSPI, ASMAN SPI, atau Auditor bisa melihat semua data
        // Support both 'Auditor' and 'AUDITOR' for case-insensitive matching
        if (in_array($namaAkses, ['KSPI', 'ASMAN SPI', 'Auditor', 'AUDITOR'])) {
            return null; // null berarti bisa melihat semua
        }

        // Load auditee relationship if not loaded
        if (!$user->relationLoaded('auditee')) {
            $user->load('auditee');
        }

        return $user->master_auditee_id ?? null;
    }

    /**
     * Check if current user can see all data (KSPI, ASMAN SPI, Auditor, BOD)
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
        
        // Support both 'Auditor' and 'AUDITOR' for case-insensitive matching
        // BOD can also see all data
        return in_array($namaAkses, ['KSPI', 'ASMAN SPI', 'Auditor', 'AUDITOR', 'BOD']);
    }

    /**
     * Check if current user is PIC (has role PIC Auditee or is assigned as PIC in any rekomendasi)
     * 
     * @return bool
     */
    public static function isPic(): bool
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
        
        // Check if user has PIC Auditee role
        return in_array($namaAkses, ['PIC Auditee', 'PIC Auditor']);
    }

    /**
     * Get current user ID
     * 
     * @return int|null
     */
    public static function getCurrentUserId(): ?int
    {
        if (!Auth::check()) {
            return null;
        }

        return Auth::id();
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

