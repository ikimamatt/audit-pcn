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

        User::factory()->create([
            'name' => 'Tapeli',
            'email' => 'demo@user.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);

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
        $this->call(MasterUserSeeder::class);
        $this->call(MasterJenisAuditSeeder::class);

        // ========================================
        // TRANSACTIONAL DATA SEEDERS
        // ========================================
        // Note: These seeders are for development/testing only
        // DO NOT run these in production!
        // ========================================
        
        // Pastikan MasterAuditeeSeeder dijalankan sebelum PerencanaanAuditSeeder
        $this->call(PerencanaanAuditSeeder::class);
        $this->call(RealisasiAuditSeeder::class);
        
        // ProgramKerjaAuditSeeder bergantung pada perencanaan_audit
        $this->call(ProgramKerjaAuditSeeder::class);
        
        // PKA related seeders (bergantung pada program_kerja_audit)
        $this->call(PkaMilestoneSeeder::class);
        $this->call(PkaRiskBasedAuditSeeder::class);
        $this->call(PkaDokumenSeeder::class);
        
        // Seeder yang bergantung pada perencanaan_audit
        $this->call(WalkthroughAuditSeeder::class);
        $this->call(TodBpmAuditSeeder::class);
        $this->call(TodBpmEvaluasiSeeder::class);
        $this->call(ToeAuditSeeder::class);
        $this->call(ToeEvaluasiSeeder::class);
        $this->call(EntryMeetingSeeder::class);
        $this->call(JadwalPkptAuditSeeder::class);
        $this->call([
            PelaporanHasilAuditSeeder::class,
            PelaporanTemuanSeeder::class,
            // PelaporanIsiLhaSeeder::class,
            // PenutupLhaRekomendasiSeeder::class,
        ]);
    }
}
