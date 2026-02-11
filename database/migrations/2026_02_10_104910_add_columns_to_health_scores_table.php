<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('health_scores', function (Blueprint $table) {
            $table->decimal('score', 5, 2)->after('id');
            $table->json('metrics_json')->nullable()->after('score');
            $table->string('trend')->default('stable')->after('metrics_json'); // 'up', 'down', 'stable'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_scores', function (Blueprint $table) {
            $table->dropColumn(['score', 'metrics_json', 'trend']);
        });
    }
};
