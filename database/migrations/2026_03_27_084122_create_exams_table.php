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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('users')->onDelete('cascade');
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->integer('durasi'); // dalam menit
            $table->integer('jumlah_peserta');
            $table->string('kode_ujian')->unique();
            $table->enum('status', ['menunggu', 'berlangsung', 'selesai'])->default('menunggu');
            $table->boolean('acak_soal')->default(false);
            $table->boolean('tampilkan_hasil')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
