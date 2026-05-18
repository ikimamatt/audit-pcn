<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// ─── Jadwal pengingat rekomendasi audit ───
// Kirim email H-30, H-7, H-3 setiap hari pukul 08:00 WIB
// (Server UTC → 08:00 WIB = 00:00 UTC)
Schedule::command('reminder:rekomendasi')
    ->dailyAt('00:00')           // sesuaikan jika server sudah WIB: ubah ke '08:00'
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/email-reminder.log'))
    ->description('Kirim email pengingat tindak lanjut rekomendasi audit (H-30, H-7, H-3)');
