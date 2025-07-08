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
        Schema::create('settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('setting_group_id')->constrained()->cascadeOnDelete();
            $table->string('key');
            $table->json('label');
            $table->json('description')->nullable();
            $table->text('value')->nullable();
            $table->string('type');
            $table->string('cast')->default('string');
            $table->string('permission')->nullable();
            $table->string('config_key')->nullable();
            $table->string('rules')->nullable();
            $table->json('options')->nullable();
            $table->json('subfields')->nullable();
            $table->json('callout')->nullable();
            $table->json('default')->nullable();
            $table->string('warning')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->unique(['setting_group_id', 'key']);
            $table->index('key');
            $table->index('setting_group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
