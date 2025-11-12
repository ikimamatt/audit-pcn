<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PkaAllSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting PKA seeding process...');
        
        // Run PKA seeders in order
        $this->call(ProgramKerjaAuditSeeder::class);
        $this->call(PkaMilestoneSeeder::class);
        $this->call(PkaRiskBasedAuditSeeder::class);
        $this->call(PkaDokumenSeeder::class);
        
        $this->command->info('PKA seeding process completed successfully!');
    }
} 