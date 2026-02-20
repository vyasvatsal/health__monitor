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
        Schema::create('temporary_images', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('mime_type');
            $table->longText('image_data'); // Base64 encoded or binary
            $table->unsignedBigInteger('original_size');
            $table->unsignedBigInteger('compressed_size');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temporary_images');
    }
};
