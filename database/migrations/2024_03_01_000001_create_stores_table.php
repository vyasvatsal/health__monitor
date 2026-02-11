<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Owner
            $table->string('name');
            $table->string('domain')->nullable();
            $table->string('api_key')->unique()->nullable(); // Ideally hashed, but raw for MVP simulation
            $table->string('tier')->default('basic'); // basic, pro, enterprise
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
