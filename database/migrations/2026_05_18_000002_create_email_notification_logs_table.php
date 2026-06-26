<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_notification_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('penutup_lha_rekomendasi_id')
                ->constrained('penutup_lha_rekomendasi')
                ->onDelete('cascade');
            $table->foreignUuid('master_user_id')
                ->constrained('master_user')
                ->onDelete('cascade');
            $table->enum('trigger_type', ['manual', 'scheduled'])->default('manual');
            $table->foreignUuid('sent_by')
                ->nullable()
                ->constrained('master_user')
                ->nullOnDelete();
            $table->enum('status', ['sent', 'failed'])->default('sent');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_notification_logs');
    }
};
