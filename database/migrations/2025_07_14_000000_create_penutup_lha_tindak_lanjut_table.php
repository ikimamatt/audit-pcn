<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penutup_lha_tindak_lanjut', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penutup_lha_rekomendasi_id')->constrained('penutup_lha_rekomendasi')->onDelete('cascade');
            $table->date('real_waktu')->nullable();
            $table->text('komentar')->nullable();
            $table->string('file_eviden')->nullable();
            $table->enum('status_tindak_lanjut', ['open', 'closed', 'on_progress'])->default('open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penutup_lha_tindak_lanjut');
    }
}; 