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
        Schema::create('page_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->string('url_path')->index(); // e.g. /dashboard
            $table->string('device_type')->default('desktop'); // mobile or desktop
            $table->integer('load_time_ms')->nullable(); // Total page load
            $table->integer('js_time_ms')->nullable(); // JS execution
            $table->json('vitals')->nullable(); // {lcp: 1200, cls: 0.1, inp: 50}
            $table->json('cta_clicks')->nullable(); // Tracking interaction with primary buttons
            $table->string('grade', 2)->nullable(); // A, B, C, D, F based on processing
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_metrics');
    }
};
