<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterKodeRiskSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('master_kode_risk')->insert([
            // STRATEGIS
            ['kelompok_risiko' => 'STRATEGIS', 'kode_risiko' => 'S.1.1', 'kelompok_risiko_detail' => 'Regulasi Pemerintah', 'deskripsi_risiko' => 'Risiko Tarif Listrik'],
            ['kelompok_risiko' => 'STRATEGIS', 'kode_risiko' => 'S.1.2', 'kelompok_risiko_detail' => 'Regulasi Pemerintah', 'deskripsi_risiko' => 'Risiko Subsidi Listrik'],
            ['kelompok_risiko' => 'STRATEGIS', 'kode_risiko' => 'S.1.3', 'kelompok_risiko_detail' => 'Regulasi Pemerintah', 'deskripsi_risiko' => 'Risiko Regulasi Daerah'],
            ['kelompok_risiko' => 'STRATEGIS', 'kode_risiko' => 'S.2.1', 'kelompok_risiko_detail' => 'Reputasi', 'deskripsi_risiko' => 'Risiko Reputasi'],
            ['kelompok_risiko' => 'STRATEGIS', 'kode_risiko' => 'S.3.1', 'kelompok_risiko_detail' => 'Organisasi Korporat', 'deskripsi_risiko' => 'Risiko Perubahan Organisasi Korporat'],
            ['kelompok_risiko' => 'STRATEGIS', 'kode_risiko' => 'S.4.1', 'kelompok_risiko_detail' => 'Portofolio Bisnis', 'deskripsi_risiko' => 'Risiko Anak Perusahaan'],
            ['kelompok_risiko' => 'STRATEGIS', 'kode_risiko' => 'S.4.2', 'kelompok_risiko_detail' => 'Portofolio Bisnis', 'deskripsi_risiko' => 'Risiko Kerjasama Strategis'],
            ['kelompok_risiko' => 'STRATEGIS', 'kode_risiko' => 'S.5.1', 'kelompok_risiko_detail' => 'Business Continuity', 'deskripsi_risiko' => 'Risiko Business Continuity Management'],
            // FINANSIAL
            ['kelompok_risiko' => 'FINANSIAL', 'kode_risiko' => 'F.1.1', 'kelompok_risiko_detail' => 'Ekonomi Makro', 'deskripsi_risiko' => 'Risiko Perubahan Kurs'],
            ['kelompok_risiko' => 'FINANSIAL', 'kode_risiko' => 'F.1.2', 'kelompok_risiko_detail' => 'Ekonomi Makro', 'deskripsi_risiko' => 'Risiko Perubahan Inflasi'],
            ['kelompok_risiko' => 'FINANSIAL', 'kode_risiko' => 'F.2.1', 'kelompok_risiko_detail' => 'Harga Energi Primer', 'deskripsi_risiko' => 'Risiko Harga Batubara'],
            ['kelompok_risiko' => 'FINANSIAL', 'kode_risiko' => 'F.2.2', 'kelompok_risiko_detail' => 'Harga Energi Primer', 'deskripsi_risiko' => 'Risiko Harga Gas'],
            ['kelompok_risiko' => 'FINANSIAL', 'kode_risiko' => 'F.2.3', 'kelompok_risiko_detail' => 'Harga Energi Primer', 'deskripsi_risiko' => 'Risiko Harga BBM'],
            ['kelompok_risiko' => 'FINANSIAL', 'kode_risiko' => 'F.2.4', 'kelompok_risiko_detail' => 'Harga Energi Primer', 'deskripsi_risiko' => 'Risiko Harga Panas Bumi'],
            ['kelompok_risiko' => 'FINANSIAL', 'kode_risiko' => 'F.2.5', 'kelompok_risiko_detail' => 'Harga Energi Primer', 'deskripsi_risiko' => 'Risiko Harga Energi Primer Lainnya'],
            ['kelompok_risiko' => 'FINANSIAL', 'kode_risiko' => 'F.3.1', 'kelompok_risiko_detail' => 'Likuiditas', 'deskripsi_risiko' => 'Risiko Tunggakan'],
            ['kelompok_risiko' => 'FINANSIAL', 'kode_risiko' => 'F.4.1', 'kelompok_risiko_detail' => 'Pinjaman', 'deskripsi_risiko' => 'Risiko Covenant'],
            ['kelompok_risiko' => 'FINANSIAL', 'kode_risiko' => 'F.4.2', 'kelompok_risiko_detail' => 'Pinjaman', 'deskripsi_risiko' => 'Risiko Suku Bunga'],
            ['kelompok_risiko' => 'FINANSIAL', 'kode_risiko' => 'F.4.3', 'kelompok_risiko_detail' => 'Pinjaman', 'deskripsi_risiko' => 'Risiko Debt Repayment'],
            ['kelompok_risiko' => 'FINANSIAL', 'kode_risiko' => 'F.5.1', 'kelompok_risiko_detail' => 'Pendapatan', 'deskripsi_risiko' => 'Risiko Pendapatan Penjualan'],
            ['kelompok_risiko' => 'FINANSIAL', 'kode_risiko' => 'F.5.2', 'kelompok_risiko_detail' => 'Pendapatan', 'deskripsi_risiko' => 'Risiko Pendapatan Lain-lain'],
            ['kelompok_risiko' => 'FINANSIAL', 'kode_risiko' => 'F.6.1', 'kelompok_risiko_detail' => 'Akunting', 'deskripsi_risiko' => 'Risiko Akunting & Pelaporan'],
            ['kelompok_risiko' => 'FINANSIAL', 'kode_risiko' => 'F.6.2', 'kelompok_risiko_detail' => 'Akunting', 'deskripsi_risiko' => 'Risiko Kontrol Internal'],
            ['kelompok_risiko' => 'FINANSIAL', 'kode_risiko' => 'F.7.1', 'kelompok_risiko_detail' => 'Pajak', 'deskripsi_risiko' => 'Risiko Pajak'],
            // OPERASIONAL
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.1.1', 'kelompok_risiko_detail' => 'Energi Primer', 'deskripsi_risiko' => 'Risiko Kontinuitas Pasokan Batubara'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.1.2', 'kelompok_risiko_detail' => 'Energi Primer', 'deskripsi_risiko' => 'Risiko Kualitas Batubara'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.1.3', 'kelompok_risiko_detail' => 'Energi Primer', 'deskripsi_risiko' => 'Risiko Kontinuitas Pasokan Gas'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.1.4', 'kelompok_risiko_detail' => 'Energi Primer', 'deskripsi_risiko' => 'Risiko Kontinuitas Pasokan BBM'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.1.5', 'kelompok_risiko_detail' => 'Energi Primer', 'deskripsi_risiko' => 'Risiko Bauran Energi (Felmix)'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.2.1', 'kelompok_risiko_detail' => 'SDM', 'deskripsi_risiko' => 'Risiko Kompetensi SDM'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.2.2', 'kelompok_risiko_detail' => 'SDM', 'deskripsi_risiko' => 'Risiko Jumlah SDM'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.2.3', 'kelompok_risiko_detail' => 'SDM', 'deskripsi_risiko' => 'Risiko Keselamatan Kerja'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.2.4', 'kelompok_risiko_detail' => 'SDM', 'deskripsi_risiko' => 'Risiko Kesejahteraan Pekerja'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.2.5', 'kelompok_risiko_detail' => 'SDM', 'deskripsi_risiko' => 'Risiko Outsourcing'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.3.1', 'kelompok_risiko_detail' => 'Sistem Tenaga Listrik', 'deskripsi_risiko' => 'Risiko Cadangan Daya Listrik'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.3.2', 'kelompok_risiko_detail' => 'Sistem Tenaga Listrik', 'deskripsi_risiko' => 'Risiko Take or Pay'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.3.3', 'kelompok_risiko_detail' => 'Sistem Tenaga Listrik', 'deskripsi_risiko' => 'Risiko Optimalisasi Operasi Sistem Tenaga Listrik'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.4.1', 'kelompok_risiko_detail' => 'Pembangkitan', 'deskripsi_risiko' => 'Risiko Ketersediaan Pembangkitan'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.4.2', 'kelompok_risiko_detail' => 'Pembangkitan', 'deskripsi_risiko' => 'Risiko Keandalan Pembangkitan'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.4.3', 'kelompok_risiko_detail' => 'Pembangkitan', 'deskripsi_risiko' => 'Risiko Derating Pembangkitan'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.4.4', 'kelompok_risiko_detail' => 'Pembangkitan', 'deskripsi_risiko' => 'Risiko Efisiensi Pembangkitan'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.4.5', 'kelompok_risiko_detail' => 'Pembangkitan', 'deskripsi_risiko' => 'Risiko IPP'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.5.1', 'kelompok_risiko_detail' => 'Penyaluran', 'deskripsi_risiko' => 'Risiko Ketersediaan Penyaluran'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.5.2', 'kelompok_risiko_detail' => 'Penyaluran', 'deskripsi_risiko' => 'Risiko Keandalan Penyaluran'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.6.1', 'kelompok_risiko_detail' => 'Distribusi', 'deskripsi_risiko' => 'Risiko Ketersediaan Jaringan Distribusi'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.6.2', 'kelompok_risiko_detail' => 'Distribusi', 'deskripsi_risiko' => 'Risiko Keandalan Jaringan Distribusi'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.6.3', 'kelompok_risiko_detail' => 'Distribusi', 'deskripsi_risiko' => 'Risiko Pertumbuhan Konsumsi Energi Listrik'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.7.1', 'kelompok_risiko_detail' => 'Pelayanan Pelanggan', 'deskripsi_risiko' => 'Risiko GCG Penyambungan Baru / Tambah Daya'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.7.2', 'kelompok_risiko_detail' => 'Pelayanan Pelanggan', 'deskripsi_risiko' => 'Risiko GCG Pembacaan Meter'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.7.3', 'kelompok_risiko_detail' => 'Pelayanan Pelanggan', 'deskripsi_risiko' => 'Risiko GCG Pelayanan Gangguan'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.7.4', 'kelompok_risiko_detail' => 'Pelayanan Pelanggan', 'deskripsi_risiko' => 'Risiko Keterbatasan Suplai'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.7.5', 'kelompok_risiko_detail' => 'Pelayanan Pelanggan', 'deskripsi_risiko' => 'Risiko Ekspektasi Pelanggan'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.7.6', 'kelompok_risiko_detail' => 'Pelayanan Pelanggan', 'deskripsi_risiko' => 'Risiko Kualitas Layanan'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.8.1', 'kelompok_risiko_detail' => 'Teknologi', 'deskripsi_risiko' => 'Risiko Obsolete Teknologi'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.8.2', 'kelompok_risiko_detail' => 'Teknologi', 'deskripsi_risiko' => 'Risiko Security Teknologi'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.9.1', 'kelompok_risiko_detail' => 'Bencana Alam', 'deskripsi_risiko' => 'Risiko Bencana Lokal'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.9.2', 'kelompok_risiko_detail' => 'Bencana Alam', 'deskripsi_risiko' => 'Risiko Bencana Nasional (Force Majeur)'],
            ['kelompok_risiko' => 'OPERASIONAL', 'kode_risiko' => 'O.9.3', 'kelompok_risiko_detail' => 'Bencana Alam', 'deskripsi_risiko' => 'Risiko Terorisme / Terrorisme'],
            // PROYEK
            ['kelompok_risiko' => 'PROYEK', 'kode_risiko' => 'P.1.1', 'kelompok_risiko_detail' => 'Perencanaan & Desain', 'deskripsi_risiko' => 'Risiko Kelayakan Proyek'],
            ['kelompok_risiko' => 'PROYEK', 'kode_risiko' => 'P.1.2', 'kelompok_risiko_detail' => 'Perencanaan & Desain', 'deskripsi_risiko' => 'Risiko Desain Proyek'],
            ['kelompok_risiko' => 'PROYEK', 'kode_risiko' => 'P.2.1', 'kelompok_risiko_detail' => 'Pendanaan Proyek', 'deskripsi_risiko' => 'Risiko Sumber Dana'],
            ['kelompok_risiko' => 'PROYEK', 'kode_risiko' => 'P.2.2', 'kelompok_risiko_detail' => 'Pendanaan Proyek', 'deskripsi_risiko' => 'Risiko Financial Closing'],
            ['kelompok_risiko' => 'PROYEK', 'kode_risiko' => 'P.2.3', 'kelompok_risiko_detail' => 'Pendanaan Proyek', 'deskripsi_risiko' => 'Risiko Disbursement'],
            ['kelompok_risiko' => 'PROYEK', 'kode_risiko' => 'P.3.1', 'kelompok_risiko_detail' => 'Pengadaan Proyek', 'deskripsi_risiko' => 'Risiko Nilai Proyek (HPS)'],
            ['kelompok_risiko' => 'PROYEK', 'kode_risiko' => 'P.3.2', 'kelompok_risiko_detail' => 'Pengadaan Proyek', 'deskripsi_risiko' => 'Risiko Kualitas Kontraktor'],
            ['kelompok_risiko' => 'PROYEK', 'kode_risiko' => 'P.3.3', 'kelompok_risiko_detail' => 'Pengadaan Proyek', 'deskripsi_risiko' => 'Risiko Gagal Lelang'],
            ['kelompok_risiko' => 'PROYEK', 'kode_risiko' => 'P.4.1', 'kelompok_risiko_detail' => 'Konstruksi', 'deskripsi_risiko' => 'Risiko Waktu Penyelesaian Proyek'],
            ['kelompok_risiko' => 'PROYEK', 'kode_risiko' => 'P.4.2', 'kelompok_risiko_detail' => 'Konstruksi', 'deskripsi_risiko' => 'Risiko Kualitas Material / Jasa'],
            ['kelompok_risiko' => 'PROYEK', 'kode_risiko' => 'P.4.3', 'kelompok_risiko_detail' => 'Konstruksi', 'deskripsi_risiko' => 'Risiko Pembayaran Termin Proyek'],
            ['kelompok_risiko' => 'PROYEK', 'kode_risiko' => 'P.5.1', 'kelompok_risiko_detail' => 'Risiko Pasca Konstruksi', 'deskripsi_risiko' => 'Risiko Serah Terima Proyek'],
            ['kelompok_risiko' => 'PROYEK', 'kode_risiko' => 'P.5.2', 'kelompok_risiko_detail' => 'Risiko Pasca Konstruksi', 'deskripsi_risiko' => 'Risiko Performance Pasca Proyek'],
            ['kelompok_risiko' => 'PROYEK', 'kode_risiko' => 'P.5.3', 'kelompok_risiko_detail' => 'Risiko Pasca Konstruksi', 'deskripsi_risiko' => 'Risiko Garansi Hasil Pekerjaan'],
            // KEPATUHAN
            ['kelompok_risiko' => 'KEPATUHAN', 'kode_risiko' => 'K.1.1', 'kelompok_risiko_detail' => 'Aspek Legal', 'deskripsi_risiko' => 'Risiko Kerjasama Pihak Ketiga'],
            ['kelompok_risiko' => 'KEPATUHAN', 'kode_risiko' => 'K.1.2', 'kelompok_risiko_detail' => 'Aspek Legal', 'deskripsi_risiko' => 'Risiko Hak Atas Kekayaan Intelektual (HAKI)'],
            ['kelompok_risiko' => 'KEPATUHAN', 'kode_risiko' => 'K.1.3', 'kelompok_risiko_detail' => 'Aspek Legal', 'deskripsi_risiko' => 'Risiko Tuntutan Hukum'],
            ['kelompok_risiko' => 'KEPATUHAN', 'kode_risiko' => 'K.1.4', 'kelompok_risiko_detail' => 'Aspek Legal', 'deskripsi_risiko' => 'Risiko Perijinan'],
            ['kelompok_risiko' => 'KEPATUHAN', 'kode_risiko' => 'K.1.5', 'kelompok_risiko_detail' => 'Aspek Legal', 'deskripsi_risiko' => 'Risiko Pembebasan Tanah'],
            ['kelompok_risiko' => 'KEPATUHAN', 'kode_risiko' => 'K.2.1', 'kelompok_risiko_detail' => 'Etika & Kecurangan (Fraud)', 'deskripsi_risiko' => 'Risiko Etika'],
            ['kelompok_risiko' => 'KEPATUHAN', 'kode_risiko' => 'K.2.2', 'kelompok_risiko_detail' => 'Etika & Kecurangan (Fraud)', 'deskripsi_risiko' => 'Risiko Kecurangan / Korupsi'],
            ['kelompok_risiko' => 'KEPATUHAN', 'kode_risiko' => 'K.3.1', 'kelompok_risiko_detail' => 'Lingkungan', 'deskripsi_risiko' => 'Risiko Dampak Lingkungan'],
            ['kelompok_risiko' => 'KEPATUHAN', 'kode_risiko' => 'K.3.2', 'kelompok_risiko_detail' => 'Lingkungan', 'deskripsi_risiko' => 'Risiko Sosial / Politik / Budaya'],
        ]);
    }
} 