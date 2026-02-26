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
        Schema::create('analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->string('url');
            $table->integer('performance_score')->nullable();
            $table->integer('accessibility_score')->nullable();
            $table->integer('best_practices_score')->nullable();
            $table->integer('seo_score')->nullable();
            $table->json('core_web_vitals')->nullable();
            $table->json('ai_insights')->nullable();
            $table->longText('desktop_screenshot')->nullable();
            $table->longText('mobile_screenshot')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analyses');
    }
};
