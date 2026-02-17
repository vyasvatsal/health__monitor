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
        Schema::create('error_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->index(); // 'js_runtime', 'php_exception', etc.
            $table->text('message');
            $table->string('file')->nullable();
            $table->integer('line')->nullable();
            $table->longText('trace')->nullable(); // JSON or text
            $table->json('context')->nullable(); // User, URL, Inputs
            $table->enum('severity', ['critical', 'warning', 'info'])->default('critical');
            $table->enum('status', ['new', 'investigating', 'resolved'])->default('new')->index();
            $table->integer('count')->default(1);
            $table->timestamp('last_seen_at')->useCurrent();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('error_logs');
    }
};
