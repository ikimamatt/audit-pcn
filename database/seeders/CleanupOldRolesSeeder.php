<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Membersihkan role lama dari master_akses_user dan memigrasikan user lama ke role baru.
 * Jalankan: php artisan db:seed --class=CleanupOldRolesSeeder
 */
class CleanupOldRolesSeeder extends Seeder
{
    // Mapping role lama → role baru
    const ROLE_MAP = [
        'Superadmin'      => 'SUPER ADMIN',
        'SUPERADMIN'      => 'SUPER ADMIN',
        'BOD'             => 'VIEW BOD',
        'Auditor'         => 'AUDITOR',
        'ASMAN KSPI'      => 'ASMAN SPI',
        'PIC Auditee'     => 'AUDITEE',
        'PIC Auditor'     => 'AUDITEE',
        'Auditee'         => 'AUDITEE',
        'Manager'         => 'AUDITEE',
        'Assistant Manager' => 'AUDITEE',
    ];

    const VALID_ROLES = ['KSPI', 'ASMAN SPI', 'AUDITOR', 'AUDITEE', 'SUPER ADMIN', 'VIEW BOD'];

    public function run(): void
    {
        // Ambil semua akses yang ada
        $allAkses = DB::table('master_akses_user')->get()->keyBy('nama_akses');

        // Proses mapping: update user yang pakai role lama
        foreach (self::ROLE_MAP as $oldRole => $newRole) {
            $oldRecord = $allAkses->get($oldRole);
            if (!$oldRecord) continue;

            $newRecord = $allAkses->get($newRole);
            if (!$newRecord) {
                echo "⚠️ Role baru '{$newRole}' tidak ditemukan, skip." . PHP_EOL;
                continue;
            }

            $count = DB::table('master_user')
                ->where('master_akses_user_id', $oldRecord->id)
                ->count();

            if ($count > 0) {
                DB::table('master_user')
                    ->where('master_akses_user_id', $oldRecord->id)
                    ->update(['master_akses_user_id' => $newRecord->id]);

                echo "✅ Migrated {$count} user(s): '{$oldRole}' → '{$newRole}'" . PHP_EOL;
            } else {
                echo "ℹ️ Tidak ada user dengan role '{$oldRole}'." . PHP_EOL;
            }
        }

        // Hapus semua role yang tidak termasuk 6 role baku
        $deleted = DB::table('master_akses_user')
            ->whereNotIn('nama_akses', self::VALID_ROLES)
            ->delete();

        echo "🗑️ Deleted {$deleted} old role(s) from master_akses_user." . PHP_EOL;
        echo "✅ Cleanup selesai. Role yang tersisa:" . PHP_EOL;

        DB::table('master_akses_user')->get()->each(function ($r) {
            echo "   - [{$r->id}] {$r->nama_akses}" . PHP_EOL;
        });
    }
}
