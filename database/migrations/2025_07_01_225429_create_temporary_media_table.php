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
        Schema::create('temporary_media', function (Blueprint $table) {
            $table->id();
            $table->string('session_id');
            $table->string('field_name');
            $table->string('model_type');
            $table->string('collection_name');
            $table->timestamps();

            $table->index(['session_id', 'field_name']);
            $table->index('created_at'); // For cleanup queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temporary_media');
    }
};
