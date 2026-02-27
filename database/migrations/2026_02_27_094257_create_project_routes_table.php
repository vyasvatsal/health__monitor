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
        Schema::create('project_routes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id')->index();
            $table->string('uri');
            $table->string('name')->nullable();
            $table->string('action')->nullable();
            $table->timestamps();

            // Setup proper foreign key
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');

            // A store shouldn't have duplicate URIs
            $table->unique(['store_id', 'uri']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_routes');
    }
};
