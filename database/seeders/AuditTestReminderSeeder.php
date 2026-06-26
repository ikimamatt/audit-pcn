<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuditTestReminderSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pastikan minimal ada data rekomendasi
        if (DB::table('penutup_lha_rekomendasi')->count() < 3) {
            $this->call(DashboardDummySeeder::class);
        }

        // 2. Cari user NIP 7905004BOPS
        $user = DB::table('master_user')->where('nip', '7905004BOPS')->first();
        if (!$user) {
            $this->call(MasterUserSeeder::class);
            $user = DB::table('master_user')->where('nip', '7905004BOPS')->first();
        }

        if ($user) {
            // 3. Cari 3 rekomendasi aktif (open atau on_progress)
            $rekomendasis = DB::table('penutup_lha_rekomendasi')
                ->whereIn('status_tindak_lanjut', ['open', 'on_progress'])
                ->limit(3)
                ->get();

            if ($rekomendasis->isEmpty()) {
                // Buat dummy temuan dan rekomendasi jika belum ada yang open/on_progress
                $this->command->error('Tidak ditemukan rekomendasi open/on_progress, jalankan DashboardDummySeeder kembali.');
                return;
            }

            foreach ($rekomendasis as $rekom) {
                $exists = DB::table('penutup_lha_rekomendasi_pic')
                    ->where('penutup_lha_rekomendasi_id', $rekom->id)
                    ->where('master_user_id', $user->id)
                    ->exists();
                if (!$exists) {
                    DB::table('penutup_lha_rekomendasi_pic')->insert([
                        'id' => (string) \Illuminate\Support\Str::uuid(),
                        'penutup_lha_rekomendasi_id' => $rekom->id,
                        'master_user_id' => $user->id,
                        'pic_type' => 'business_contact',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            $this->command->info("✅ Berhasil menetapkan " . $rekomendasis->count() . " rekomendasi aktif ke user 7905004BOPS (PIC Business Contact).");
        } else {
            $this->command->error("❌ User dengan NIP 7905004BOPS tidak ditemukan di master_user!");
        }
    }
}
