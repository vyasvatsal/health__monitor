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
        Schema::create('error_groups', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->string('fingerprint')->unique();
            $table->string('title');
            $table->string('status')->default('open'); // open, resolved, ignored
            $table->timestamp('last_seen_at')->useCurrent();
            $table->unsignedInteger('count')->default(1);
            $table->timestamps();
        });

        Schema::create('error_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('error_group_id')->constrained('error_groups')->cascadeOnDelete();
            $table->string('message');
            $table->json('payload')->nullable();
            $table->text('stack_trace')->nullable();
            $table->timestamp('occurred_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('error_events');
        Schema::dropIfExists('error_groups');
    }
};
