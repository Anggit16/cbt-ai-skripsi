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
        Schema::table('questions', function (Blueprint $table) {
            // Gambar untuk soal (PG dan Essay)
            $table->string('image')->nullable()->after('question_text');

            // Gambar untuk opsi jawaban PG (disimpan sebagai JSON)
            $table->json('option_images')->nullable()->after('options');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['image', 'option_images']);
        });
    }
};
