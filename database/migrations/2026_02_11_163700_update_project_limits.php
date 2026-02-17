<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('users', 'max_projects')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('max_projects')->default(5)->change();
            });
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('max_projects')->default(5);
            });
        }

        // Update existing users
        DB::table('users')->update(['max_projects' => 5]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'max_projects')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('max_projects')->default(1)->change();
            });
        }
    }
};
