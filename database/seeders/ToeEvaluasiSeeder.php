<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ToeEvaluasiSeeder extends Seeder
{
    public function run(): void
    {
        $toeAudits = DB::table('toe_audit')->take(2)->get();
        if ($toeAudits->count() < 2) {
            $this->command->warn('Tidak cukup data toe_audit. Skipping ToeEvaluasiSeeder.');
            return;
        }
        $data = [
            [
                'toe_audit_id' => $toeAudits[0]->id,
                'hasil_evaluasi' => 'Efektif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'toe_audit_id' => $toeAudits[1]->id,
                'hasil_evaluasi' => 'Efektif Sebagian',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        foreach ($data as &$row) {
            $row['id'] = (string) \Illuminate\Support\Str::uuid();
        }
        DB::table('toe_evaluasi')->insert($data);
    }
}
