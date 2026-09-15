<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gajis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained()->cascadeOnDelete();
            $table->date('periode_awal');
            $table->date('periode_akhir');
            $table->unsignedBigInteger('gaji_pokok')->default(0);
            $table->unsignedBigInteger('lembur')->default(0);
            $table->unsignedBigInteger('pinjaman_karyawan')->default(0);
            $table->timestamp('email_sent_at')->nullable();
            $table->timestamp('whatsapp_sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gajis');
    }
};
