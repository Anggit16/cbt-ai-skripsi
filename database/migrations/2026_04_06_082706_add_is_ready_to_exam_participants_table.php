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
        Schema::table('exam_participants', function (Blueprint $table) {
            $table->boolean('is_ready')->default(false)->after('status');
            $table->timestamp('ready_at')->nullable()->after('is_ready');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_participants', function (Blueprint $table) {
            $table->dropColumn(['is_ready', 'ready_at']);
        });
    }
};
