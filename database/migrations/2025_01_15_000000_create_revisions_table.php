<?php

declare(strict_types=1);

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
        Schema::create('revisions', function (Blueprint $table) {
            $table->id();
            $table->morphs('revisionable');
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('action'); // create, update, delete, publish, revert
            $table->string('version')->nullable(); // semantic versioning
            $table->json('data')->nullable(); // snapshot of the model data
            $table->json('changes')->nullable(); // what changed in this revision
            $table->json('metadata')->nullable(); // additional context
            $table->text('description')->nullable(); // human-readable description
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['revisionable_type', 'revisionable_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index(['is_published', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revisions');
    }
}; 