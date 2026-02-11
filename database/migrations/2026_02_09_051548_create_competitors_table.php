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
        Schema::create('competitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('url');
            $table->timestamp('last_audit_at')->nullable();
            $table->timestamps();
        });

        Schema::create('benchmark_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competitor_id')->constrained()->cascadeOnDelete();
            $table->integer('my_ttfb_ms')->nullable();
            $table->integer('competitor_ttfb_ms')->nullable();
            $table->integer('my_size_kb')->nullable();
            $table->integer('competitor_size_kb')->nullable();
            $table->string('winner')->nullable(); // me, them, tie
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('benchmark_results');
        Schema::dropIfExists('competitors');
    }
};
