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
        if (!self::hasAsmanKspiUser()) {
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
}

