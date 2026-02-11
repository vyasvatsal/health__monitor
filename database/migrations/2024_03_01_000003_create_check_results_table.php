<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('check_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('health_check_id')->constrained()->cascadeOnDelete();
            $table->string('status'); // ok, warning, critical
            $table->integer('latency_ms')->nullable();
            $table->json('payload')->nullable(); // detailed diagnostics
            $table->timestamp('created_at')->useCurrent();

            // Index for fast dashboard queries
            $table->index(['health_check_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('check_results');
    }
};
