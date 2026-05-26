<?php

namespace Database\Seeders;

use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * This seeds ALL data (master data + transactional data).
     * 
     * If you only want to seed master data, use:
     * php artisan db:seed --class=MasterDataSeeder
     * 
     * @see MasterDataSeeder for seeding master data only
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'demo@user.com'],
            [
                'name' => 'Tapeli',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
            ]
        );

        // ========================================
        // MASTER DATA SEEDERS
        // ========================================
        // Tip: You can run only these seeders using:
        // php artisan db:seed --class=MasterDataSeeder
        // ========================================
        $this->call(MasterKodeAoiSeeder::class);
        $this->call(MasterKodeRiskSeeder::class);
        $this->call(MasterAuditeeSeeder::class);
        $this->call(MasterAksesUserSeeder::class);
        $this->call(MasterUnitSeeder::class); // seeds master_region and master_area from SQL dumps
        $this->call(MasterUserSeeder::class);
        $this->call(MasterJenisAuditSeeder::class);

        // ========================================
        // TRANSACTIONAL DATA SEEDERS
        // ========================================
        // Note: These seeders are for development/testing only
        // DO NOT run these in production!
        // ========================================
        
        // ── Perencanaan Audit ──────────────────────────────────────
        // $this->call(PerencanaanAuditSeeder::class);
        // $this->call(RealisasiAuditSeeder::class);

        // // ── Program Kerja Audit (PKA) ──────────────────────────────
        // // ProgramKerjaAuditSeeder sudah termasuk:
        // //   - pka_proses_bisnis, pka_risiko, pka_kontrol (hierarki baru)
        // //   - pka_dokumen
        // // PkaMilestoneSeeder: milestone per PKA
        // $this->call(ProgramKerjaAuditSeeder::class);
        // $this->call(PkaMilestoneSeeder::class);

        // // ── Walkthrough (bergantung pada milestone Walkthrough di PKA) ──
        // $this->call(WalkthroughAuditSeeder::class);

        // // ── TOD (bergantung pada Walkthrough + PKA hierarki) ──────
        // // TodBpmAuditSeeder sudah termasuk tod_bpm_risiko, tod_bpm_kontrol, tod_bpm_evaluasi
        // $this->call(TodBpmAuditSeeder::class);

        // // ── TOE (bergantung pada TOD + PKA hierarki) ──────────────
        // // ToeAuditSeeder sudah termasuk toe_risiko, toe_kontrol, toe_evaluasi
        // $this->call(ToeAuditSeeder::class);

        // // ── Modul selanjutnya ─────────────────────────────────────
        // $this->call(EntryMeetingSeeder::class);
        // $this->call(JadwalPkptAuditSeeder::class);
        // $this->call([
        //     PelaporanHasilAuditSeeder::class,
        //     PelaporanTemuanSeeder::class,
        // ]);

        // // ── Dashboard Analytical Dummy Data ───────────────────────
        // $this->call(DashboardDummySeeder::class);
    }
}
