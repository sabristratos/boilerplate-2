<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Google OAuth fields
            $table->string('google_id')->nullable()->unique();
            $table->string('google_token')->nullable();
            $table->string('google_refresh_token')->nullable();
            
            // Facebook OAuth fields
            $table->string('facebook_id')->nullable()->unique();
            $table->string('facebook_token')->nullable();
            $table->string('facebook_refresh_token')->nullable();
            
            // Make password nullable for social login users
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'google_id',
                'google_token',
                'google_refresh_token',
                'facebook_id',
                'facebook_token',
                'facebook_refresh_token',
            ]);
            
            // Make password required again
            $table->string('password')->nullable(false)->change();
        });
    }
};
