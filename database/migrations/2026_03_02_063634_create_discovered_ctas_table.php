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
        Schema::create('discovered_ctas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crawled_page_id')->constrained()->cascadeOnDelete();
            $table->text('text')->nullable();
            $table->string('tag')->nullable();
            $table->string('href', 2048)->nullable();
            $table->text('css_classes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discovered_ctas');
    }
};
