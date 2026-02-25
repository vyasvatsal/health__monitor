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
        Schema::create('performance_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->string('route_name')->nullable();
            $table->string('method', 20)->nullable();
            $table->string('url')->nullable();
            $table->integer('duration_ms')->default(0);
            $table->integer('memory_usage_mb')->default(0);
            $table->string('env', 20)->default('production');
            $table->json('payload')->nullable(); // Store headers or extra data
            $table->timestamp('occurred_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_transactions');
    }
};
