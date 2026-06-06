<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengingat Tindak Lanjut Rekomendasi Audit</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: #f4f7f9;
            color: #374151;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        .wrapper {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        /* ── Header ── */
        .header {
            background: #ffffff;
            padding: 40px 40px 25px;
            text-align: center;
        }

        .header img {
            max-height: 55px;
            width: auto;
            object-fit: contain;
            margin-bottom: 24px;
        }

        .header .logo-text {
            color: #008dd5;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .header h1 {
            color: #111827;
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .header .subtitle {
            color: #6b7280;
            font-size: 15px;
            font-weight: 500;
        }

        /* ── Alert Banner ── */
        .alert-banner {
            padding: 16px 40px;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .alert-overdue {
            background: #fef2f2;
            color: #991b1b;
            border-bottom: 1px solid #fee2e2;
            border-top: 1px solid #fee2e2;
        }

        .alert-critical {
            background: #fffbeb;
            color: #92400e;
            border-bottom: 1px solid #fef3c7;
            border-top: 1px solid #fef3c7;
        }

        .alert-warning {
            background: #fff7ed;
            color: #c2410c;
            border-bottom: 1px solid #ffedd5;
            border-top: 1px solid #ffedd5;
        }

        .alert-info {
            background: #eff6ff;
            color: #1d4ed8;
            border-bottom: 1px solid #dbeafe;
            border-top: 1px solid #dbeafe;
        }

        /* ── Body ── */
        .body {
            padding: 40px;
        }

        .greeting {
            font-size: 16px;
            margin-bottom: 16px;
            color: #1f2937;
        }

        .greeting strong {
            color: #111827;
            font-weight: 700;
        }

        .intro-text {
            font-size: 15px;
            color: #4b5563;
            margin-bottom: 32px;
            line-height: 1.7;
        }

        /* ── Countdown Box ── */
        .countdown-box {
            border-radius: 16px;
            padding: 30px 20px;
            text-align: center;
            margin-bottom: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .countdown-box.overdue {
            background: linear-gradient(145deg, #ffffff, #fef2f2);
            border: 1px solid #fecaca;
        }

        .countdown-box.critical {
            background: linear-gradient(145deg, #ffffff, #fffbeb);
            border: 1px solid #fde68a;
        }

        .countdown-box.warning {
            background: linear-gradient(145deg, #ffffff, #fff7ed);
            border: 1px solid #fed7aa;
        }

        .countdown-box.info {
            background: linear-gradient(145deg, #ffffff, #eff6ff);
            border: 1px solid #bfdbfe;
        }

        .countdown-box .days-number {
            font-size: 64px;
            font-weight: 900;
            line-height: 1;
            margin-bottom: 8px;
        }

        .countdown-box.overdue .days-number {
            color: #dc2626;
        }

        .countdown-box.critical .days-number {
            color: #d97706;
        }

        .countdown-box.warning .days-number {
            color: #ea580c;
        }

        .countdown-box.info .days-number {
            color: #2563eb;
        }

        .countdown-box .days-label {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .countdown-box.overdue .days-label {
            color: #b91c1c;
        }

        .countdown-box.critical .days-label {
            color: #b45309;
        }

        .countdown-box.warning .days-label {
            color: #c2410c;
        }

        .countdown-box.info .days-label {
            color: #1d4ed8;
        }

        /* ── Info Table ── */
        .info-section {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            margin-bottom: 32px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }

        .info-section .section-title {
            background: #f8fafc;
            color: #475569;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            padding: 16px 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .info-row {
            display: flex;
            border-bottom: 1px solid #f3f4f6;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            width: 38%;
            padding: 16px 20px;
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            background: #fafafa;
        }

        .info-value {
            width: 62%;
            padding: 16px 20px;
            font-size: 14px;
            font-weight: 500;
            color: #111827;
            word-break: break-word;
        }

        /* Status badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .badge::before {
            content: '';
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            margin-right: 6px;
        }

        .badge-open {
            background: #fef9c3;
            color: #a16207;
        }

        .badge-open::before {
            background: #eab308;
        }

        .badge-progress {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-progress::before {
            background: #3b82f6;
        }

        .badge-closed {
            background: #dcfce7;
            color: #166534;
        }

        .badge-closed::before {
            background: #22c55e;
        }

        /* ── Rekomendasi Box ── */
        .rekomendasi-box {
            background: #f8fafc;
            border-left: 4px solid #008dd5;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 32px;
        }

        .rekomendasi-box .box-label {
            font-size: 12px;
            font-weight: 700;
            color: #008dd5;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin-bottom: 12px;
        }

        .rekomendasi-box p {
            font-size: 15px;
            color: #334155;
            line-height: 1.7;
        }

        /* ── CTA Button ── */
        .cta-section {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .cta-btn {
            display: inline-block;
            background: #008dd5;
            color: #ffffff !important;
            text-decoration: none;
            padding: 16px 40px;
            border-radius: 50px;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 14px 0 rgba(0, 141, 213, 0.3);
            transition: all 0.2s ease;
        }

        .cta-note {
            font-size: 13px;
            color: #94a3b8;
            margin-top: 16px;
        }

        /* ── Footer ── */
        .footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 30px 40px;
            text-align: center;
        }

        .footer p {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 6px;
        }

        .footer .company {
            font-weight: 700;
            color: #334155;
            font-size: 14px;
        }

        /* Mobile optimization */
        @media only screen and (max-width: 600px) {
            .wrapper {
                margin: 0;
                border-radius: 0;
                box-shadow: none;
            }

            .header {
                padding: 30px 20px 20px;
            }

            .body {
                padding: 24px 20px;
            }

            .footer {
                padding: 30px 20px;
            }

            .info-label {
                width: 100%;
                display: block;
                border-right: none;
                padding: 12px 16px 4px;
                background: transparent;
            }

            .info-value {
                width: 100%;
                display: block;
                padding: 0 16px 16px;
            }

            .info-row {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">

        {{-- ══ Header ══ --}}
        <div class="header">
            <div>
                <img src="{{ $message->embed(public_path('images/logo-pln.png')) }}" alt="Logo PLN Paguntaka Cahaya Nusantara">
            </div>
            <div class="logo-text">PT PLN Paguntaka Cahaya Nusantara · SPI</div>
            <h1>🔔 Pengingat Tindak Lanjut</h1>
            <div class="subtitle">Rekomendasi Audit Internal</div>
        </div>

        {{-- ══ Alert Banner ══ --}}
        @php
            $sisa = $sisaHari;
            if ($sisa < 0) {
                $alertClass = 'alert-overdue';
                $alertText = '⚠️ BATAS WAKTU TELAH TERLEWAT! Segera tindaklanjuti rekomendasi ini.';
            } elseif ($sisa <= 3) {
                $alertClass = 'alert-critical';
                $alertText = '🚨 KRITIS: Hanya tersisa ' . $sisa . ' hari lagi!';
            } elseif ($sisa <= 7) {
                $alertClass = 'alert-warning';
                $alertText = '⏰ MENDESAK: Rekomendasi harus diselesaikan dalam ' . $sisa . ' hari.';
            } else {
                $alertClass = 'alert-info';
                $alertText = '📅 Pengingat: Masih tersisa ' . $sisa . ' hari sebelum batas waktu.';
            }

            if ($sisa < 0) {
                $boxClass = 'overdue';
            } elseif ($sisa <= 3) {
                $boxClass = 'critical';
            } elseif ($sisa <= 7) {
                $boxClass = 'warning';
            } else {
                $boxClass = 'info';
            }

            $statusLabel = match ($rekomendasi->status_tindak_lanjut) {
                'open' => '<span class="badge badge-open">Open</span>',
                'on_progress' => '<span class="badge badge-progress">On Progress</span>',
                'closed' => '<span class="badge badge-closed">Closed</span>',
                default => '<span class="badge">-</span>',
            };

            $targetDate = \Carbon\Carbon::parse($rekomendasi->target_waktu)->isoFormat('D MMMM YYYY');
        @endphp

        <div class="alert-banner {{ $alertClass }}">{{ $alertText }}</div>

        {{-- ══ Body ══ --}}
        <div class="body">
            <p class="greeting">Yth. <strong>{{ $penerima->nama }}</strong> ({{ $penerima->jabatan ?? '-' }}),</p>
            <p class="intro-text">
                Kami menginformasikan bahwa terdapat rekomendasi audit yang memerlukan perhatian dan tindak lanjut
                segera dari Bapak/Ibu.
                Mohon untuk segera menyelesaikan tindak lanjut sesuai batas waktu yang telah ditetapkan.
            </p>

            {{-- Countdown --}}
            <div class="countdown-box {{ $boxClass }}">
                <div class="days-number">
                    @if($sisa < 0) {{ abs($sisa) }} @else {{ $sisa }} @endif
                </div>
                <div class="days-label">
                    @if($sisa < 0) Hari Terlambat @else Hari Tersisa @endif
                </div>
            </div>

            {{-- Info Audit --}}
            <div class="info-section">
                <div class="section-title">📋 Informasi Audit</div>
                <div class="info-row">
                    <div class="info-label">No. Surat Tugas</div>
                    <div class="info-value"><strong>{{ $nomorSuratTugas }}</strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Nomor ISS</div>
                    <div class="info-value"><strong>{{ $nomorIss }}</strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Auditee</div>
                    <div class="info-value">{{ $namaAuditee }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Target Waktu</div>
                    <div class="info-value"><strong>{{ $targetDate }}</strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status Saat Ini</div>
                    <div class="info-value">{!! $statusLabel !!}</div>
                </div>
            </div>

            {{-- Rekomendasi --}}
            <div class="rekomendasi-box">
                <div class="box-label">📌 Rekomendasi</div>
                <p>{{ $rekomendasi->rekomendasi }}</p>
            </div>

            {{-- Rencana Aksi --}}
            @if($rekomendasi->rencana_aksi)
                <div class="rekomendasi-box" style="border-left-color: #16a34a; margin-bottom: 28px;">
                    <div class="box-label" style="color: #16a34a;">✅ Rencana Aksi</div>
                    <p>{{ $rekomendasi->rencana_aksi }}</p>
                </div>
            @endif

            {{-- CTA --}}
            <div class="cta-section">
                <a href="{{ url('/audit/pemantauan?nomor_surat_tugas=' . urlencode($nomorSuratTugas)) }}"
                    class="cta-btn">
                    Lihat Detail & Update Tindak Lanjut →
                </a>
                <p class="cta-note">Klik tombol di atas untuk masuk ke sistem dan memperbarui status tindak lanjut.</p>
            </div>
        </div>

        {{-- ══ Footer ══ --}}
        <div class="footer">
            <p class="company">Satuan Pengawas Internal (SPI)</p>
            <p>PLN Paguntaka Cahaya Nusantara</p>
            <p style="margin-top: 8px; color: #94a3b8; font-size: 12px;">Email ini dikirim secara otomatis oleh sistem
                Audit PLN Paguntaka Cahaya Nusantara. Mohon tidak membalas email ini.</p>
        </div>

    </div>
</body>

</html>