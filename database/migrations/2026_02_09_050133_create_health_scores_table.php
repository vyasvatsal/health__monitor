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
        Schema::create('health_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->integer('score'); // 0-100
            $table->float('metric_availability');
            $table->float('metric_performance');
            $table->float('metric_incidents');
            $table->date('recorded_at');
            $table->timestamps();

            $table->index(['store_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_scores');
    }
};
