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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setting_group_id')->constrained()->cascadeOnDelete();
            $table->string('key')->unique();
            $table->json('label');
            $table->json('description')->nullable();
            $table->string('type');
            $table->string('cast')->nullable();
            $table->text('rules')->nullable();
            $table->text('value')->nullable();
            $table->string('permission')->nullable();
            $table->string('config_key')->nullable();
            $table->json('options')->nullable();
            $table->json('subfields')->nullable();
            $table->json('callout')->nullable();
            $table->timestamps();
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
