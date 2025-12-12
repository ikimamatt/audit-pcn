<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

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
     * Can approve if status is 'approved_level1'
     */
    public static function canApproveLevel2($item): bool
    {
        if (!self::isKspi()) {
            return false;
        }

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
}

