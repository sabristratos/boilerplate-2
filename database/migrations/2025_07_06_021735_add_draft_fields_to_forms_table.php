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
        Schema::table('forms', function (Blueprint $table) {
            $table->json('draft_name')->nullable()->after('name');
            $table->json('draft_elements')->nullable()->after('elements');
            $table->json('draft_settings')->nullable()->after('settings');
            $table->timestamp('last_draft_at')->nullable()->after('draft_settings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->dropColumn(['draft_name', 'draft_elements', 'draft_settings', 'last_draft_at']);
        });
    }
};
