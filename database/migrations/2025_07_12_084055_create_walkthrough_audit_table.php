<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('walkthrough_audit', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('perencanaan_audit_id');
            $table->date('tanggal_walkthrough');
            $table->string('auditee_nama');
            $table->text('hasil_walkthrough');
            $table->enum('status_approval', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('perencanaan_audit_id')->references('id')->on('perencanaan_audit')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('walkthrough_audit');
    }
};
