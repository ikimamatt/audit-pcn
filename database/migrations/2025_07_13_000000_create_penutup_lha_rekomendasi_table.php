<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penutup_lha_rekomendasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelaporan_isi_lha_id')->constrained('pelaporan_isi_lha')->onDelete('cascade');
            $table->text('rekomendasi'); // max 5000
            $table->text('rencana_aksi'); // max 5000
            $table->text('eviden_rekomendasi'); // max 5000
            $table->string('pic_rekomendasi', 500);
            $table->date('target_waktu');
            $table->date('real_waktu')->nullable();
            $table->text('komentar')->nullable();
            $table->string('file_eviden')->nullable();
            $table->enum('status_tindak_lanjut', ['open', 'closed', 'on_progress'])->default('open');
            $table->enum('status_approval', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('master_user')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penutup_lha_rekomendasi');
    }
}; 