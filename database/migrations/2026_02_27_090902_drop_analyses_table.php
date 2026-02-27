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
        Schema::dropIfExists('analyses');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->string('url');
            $table->integer('performance_score');
            $table->integer('accessibility_score')->default(0);
            $table->integer('best_practices_score')->default(0);
            $table->integer('seo_score')->default(0);
            $table->timestamps();
        });
    }
};
