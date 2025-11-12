<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entry_meeting', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->unsignedBigInteger('auditee_id');
            $table->string('file_undangan');
            $table->string('file_absensi');
            $table->enum('status_approval', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('auditee_id')->references('id')->on('master_auditee')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entry_meeting');
    }
}; 