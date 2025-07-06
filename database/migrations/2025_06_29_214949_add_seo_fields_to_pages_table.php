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
        Schema::table('pages', function (Blueprint $table) {
            $table->json('meta_keywords')->nullable()->after('meta_description');
            $table->json('og_title')->nullable()->after('meta_keywords');
            $table->json('og_description')->nullable()->after('og_title');
            $table->json('og_image')->nullable()->after('og_description');
            $table->json('twitter_title')->nullable()->after('og_image');
            $table->json('twitter_description')->nullable()->after('twitter_title');
            $table->json('twitter_image')->nullable()->after('twitter_description');
            $table->json('twitter_card_type')->nullable()->after('twitter_image');
            $table->json('canonical_url')->nullable()->after('twitter_card_type');
            $table->json('structured_data')->nullable()->after('canonical_url');
            $table->boolean('no_follow')->default(false)->after('no_index');
            $table->boolean('no_archive')->default(false)->after('no_follow');
            $table->boolean('no_snippet')->default(false)->after('no_archive');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn([
                'meta_keywords',
                'og_title',
                'og_description',
                'og_image',
                'twitter_title',
                'twitter_description',
                'twitter_image',
                'twitter_card_type',
                'canonical_url',
                'structured_data',
                'no_follow',
                'no_archive',
                'no_snippet',
            ]);
        });
    }
};
