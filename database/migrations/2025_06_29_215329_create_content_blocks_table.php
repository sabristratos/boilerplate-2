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
        Schema::create('content_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->json('data')->nullable();
            $table->json('settings')->nullable();
            $table->json('draft_data')->nullable();
            $table->json('draft_settings')->nullable();
            $table->boolean('visible')->default(true);
            $table->boolean('draft_visible')->nullable();
            $table->integer('order')->default(0);
            $table->timestamp('last_draft_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_blocks');
    }
};
