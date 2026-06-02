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
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan kolom role setelah email
            $table->enum('role', ['guru', 'siswa'])->default('siswa')->after('email');

            // Kolom untuk guru
            $table->string('mata_pelajaran')->nullable()->after('role');

            // Kolom untuk siswa
            $table->string('kelas')->nullable()->after('mata_pelajaran');
            $table->string('jurusan')->nullable()->after('kelas');
            $table->string('nomor_absen')->nullable()->after('jurusan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
             $table->dropColumn([
                'role',
                'mata_pelajaran',
                'kelas',
                'jurusan',
                'nomor_absen'
            ]);
        });
    }
};
