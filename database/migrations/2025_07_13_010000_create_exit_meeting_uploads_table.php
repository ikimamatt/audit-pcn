<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exit_meeting_uploads', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_exit_meeting');
            $table->unsignedBigInteger('auditee_id');
            $table->string('file_undangan');
            $table->string('file_absensi');
            $table->enum('status_approval_undangan', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('approved_by_undangan')->nullable();
            $table->timestamp('approved_at_undangan')->nullable();
            $table->enum('status_approval_absensi', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('approved_by_absensi')->nullable();
            $table->timestamp('approved_at_absensi')->nullable();
            $table->timestamps();

            $table->foreign('auditee_id')->references('id')->on('master_auditee');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exit_meeting_uploads');
    }
}; 