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
            if (!Schema::hasColumn('health_scores', 'score')) {
                $table->decimal('score', 5, 2)->after('id');
            }
            if (!Schema::hasColumn('health_scores', 'metrics_json')) {
                $table->json('metrics_json')->nullable()->after('score');
            }
            if (!Schema::hasColumn('health_scores', 'trend')) {
                $table->string('trend')->default('stable')->after('metrics_json');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_scores', function (Blueprint $table) {
            //
        });
    }
};
