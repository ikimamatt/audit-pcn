<?php

namespace App\Mail;

use App\Models\PenutupLhaRekomendasi;
use App\Models\MasterData\MasterUser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class ReminderRekomendasiMail extends Mailable
{
    use Queueable, SerializesModels;

    public PenutupLhaRekomendasi $rekomendasi;
    public MasterUser $penerima;
    public string $triggerType; // 'manual' | 'scheduled'
    public int $sisaHari;
    public string $nomorSuratTugas;
    public string $nomorIss;
    public string $namaAuditee;

    /**
     * Create a new message instance.
     */
    public function __construct(
        PenutupLhaRekomendasi $rekomendasi,
        MasterUser $penerima,
        string $triggerType = 'manual'
    ) {
        $this->rekomendasi = $rekomendasi;
        $this->penerima    = $penerima;
        $this->triggerType = $triggerType;

        // Hitung sisa hari
        $targetWaktu    = Carbon::parse($rekomendasi->target_waktu);
        $today          = Carbon::today();
        $this->sisaHari = (int) $today->diffInDays($targetWaktu, false);

        // Ambil nomor surat tugas
        $this->nomorSuratTugas = optional(
            optional(optional($rekomendasi->temuan)->pelaporanHasilAudit)->perencanaanAudit
        )->nomor_surat_tugas ?? '-';

        // Ambil nomor ISS
        $this->nomorIss = optional($rekomendasi->temuan)->nomor_iss ?? '-';

        // Ambil nama auditee
        $this->namaAuditee = optional(
            optional(optional(optional($rekomendasi->temuan)->pelaporanHasilAudit)->perencanaanAudit)->auditee
        )->divisi ?? '-';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subjectPrefix = $this->triggerType === 'scheduled' ? '[Pengingat Otomatis]' : '[Pengingat]';
        $sisaLabel     = $this->sisaHari >= 0
            ? "Sisa {$this->sisaHari} Hari"
            : 'TELAH MELEWATI BATAS WAKTU';

        return new Envelope(
            subject: "{$subjectPrefix} Tindak Lanjut Rekomendasi Audit - {$this->nomorIss} ({$sisaLabel})",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reminder-rekomendasi',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
