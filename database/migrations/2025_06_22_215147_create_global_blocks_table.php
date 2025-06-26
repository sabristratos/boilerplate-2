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
        Schema::create('global_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('An internal name for easy identification, e.g., "Homepage CTA Banner".');
            $table->string('type')->comment('The type of block, e.g., "hero-section".');
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_blocks');
    }
};
