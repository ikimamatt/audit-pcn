<?php

namespace App\Console\Commands;

use App\Mail\ReminderRekomendasiMail;
use App\Models\EmailNotificationLog;
use App\Models\PenutupLhaRekomendasi;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendReminderRekomendasiEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Notifikasi otomatis dikirim ke seluruh PIC (business_contact, approval_1_spi, approval_2_spi)
     * untuk rekomendasi yang:
     *   - Status open atau on_progress
     *   - Target waktu 30, 7, atau 3 hari ke depan (±1 hari toleransi)
     *   - Belum pernah dinotifikasi hari ini
     */
    protected $signature   = 'reminder:rekomendasi {--dry-run : Preview saja tanpa kirim email}';
    protected $description = 'Kirim email pengingat tindak lanjut rekomendasi audit secara otomatis';

    // Threshold hari yang akan dinotifikasi
    private array $thresholds = [30, 7, 3];

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $today    = Carbon::today();

        if ($isDryRun) {
            $this->warn('=== MODE DRY-RUN: Email tidak akan benar-benar dikirim ===');
        }

        $this->info("📧 Memulai pengiriman reminder rekomendasi audit...");
        $this->info("   Tanggal: {$today->isoFormat('D MMMM YYYY')}");
        $this->newLine();

        $totalSent   = 0;
        $totalFailed = 0;
        $totalSkip   = 0;

        foreach ($this->thresholds as $threshold) {
            // Cari rekomendasi dengan target_waktu tepat $threshold hari dari sekarang
            $targetDate = $today->copy()->addDays($threshold);

            $rekomendasis = PenutupLhaRekomendasi::with([
                'temuan.pelaporanHasilAudit.perencanaanAudit.auditee',
                'picUsers',
            ])
                ->whereIn('status_tindak_lanjut', ['open', 'on_progress'])
                ->whereDate('target_waktu', $targetDate)
                // Belum dinotifikasi hari ini
                ->where(function ($q) use ($today) {
                    $q->whereNull('last_notified_at')
                      ->orWhereDate('last_notified_at', '<', $today);
                })
                ->get();

            if ($rekomendasis->isEmpty()) {
                $this->line("   H-{$threshold}: Tidak ada rekomendasi.");
                continue;
            }

            $this->info("   H-{$threshold}: Ditemukan {$rekomendasis->count()} rekomendasi.");

            foreach ($rekomendasis as $rekomendasi) {
                // Ambil semua PIC (business_contact + approver SPI)
                $pics = $rekomendasi->picUsers()->whereNotNull('email')->get();

                if ($pics->isEmpty()) {
                    $this->warn("     ⚠ Rekomendasi #{$rekomendasi->id}: Tidak ada PIC dengan email terdaftar. Dilewati.");
                    $totalSkip++;
                    continue;
                }

                foreach ($pics as $pic) {
                    $this->line("     → Kirim ke: {$pic->nama} <{$pic->email}>");

                    if ($isDryRun) {
                        $totalSent++;
                        continue;
                    }

                    try {
                        Mail::to($pic->email, $pic->nama)
                            ->send(new ReminderRekomendasiMail($rekomendasi, $pic, 'scheduled'));

                        // Catat ke log
                        EmailNotificationLog::create([
                            'penutup_lha_rekomendasi_id' => $rekomendasi->id,
                            'master_user_id'             => $pic->id,
                            'trigger_type'               => 'scheduled',
                            'sent_by'                    => null,
                            'status'                     => 'sent',
                            'sent_at'                    => now(),
                        ]);

                        $totalSent++;
                    } catch (\Throwable $e) {
                        $this->error("     ✗ Gagal kirim ke {$pic->email}: {$e->getMessage()}");

                        // Catat kegagalan ke log
                        EmailNotificationLog::create([
                            'penutup_lha_rekomendasi_id' => $rekomendasi->id,
                            'master_user_id'             => $pic->id,
                            'trigger_type'               => 'scheduled',
                            'sent_by'                    => null,
                            'status'                     => 'failed',
                            'error_message'              => $e->getMessage(),
                            'sent_at'                    => now(),
                        ]);

                        $totalFailed++;
                    }
                }

                if (! $isDryRun) {
                    // Tandai sudah dinotifikasi hari ini
                    $rekomendasi->update(['last_notified_at' => now()]);
                }
            }
        }

        $this->newLine();
        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Email Terkirim',  $totalSent],
                ['Email Gagal',     $totalFailed],
                ['Rekomendasi Skip', $totalSkip],
            ]
        );

        $this->info('✅ Selesai.');
        return self::SUCCESS;
    }
}
