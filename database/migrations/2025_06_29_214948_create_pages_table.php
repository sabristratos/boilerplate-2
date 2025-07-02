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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->string('slug')->unique();
            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();
            $table->boolean('no_index')->default(false);
            // Draft fields
            $table->json('draft_title')->nullable();
            $table->string('draft_slug')->nullable();
            $table->json('draft_meta_title')->nullable();
            $table->json('draft_meta_description')->nullable();
            $table->json('draft_meta_keywords')->nullable();
            $table->json('draft_og_title')->nullable();
            $table->json('draft_og_description')->nullable();
            $table->json('draft_og_image')->nullable();
            $table->json('draft_twitter_title')->nullable();
            $table->json('draft_twitter_description')->nullable();
            $table->json('draft_twitter_image')->nullable();
            $table->json('draft_twitter_card_type')->nullable();
            $table->json('draft_canonical_url')->nullable();
            $table->json('draft_structured_data')->nullable();
            $table->boolean('draft_no_index')->nullable();
            $table->boolean('draft_no_follow')->nullable();
            $table->boolean('draft_no_archive')->nullable();
            $table->boolean('draft_no_snippet')->nullable();
            $table->timestamp('last_draft_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
