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
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('enable_rush_delivery')->default(false)->after('enable_due_date');
            $table->json('rush_delivery_options')->nullable()->after('enable_rush_delivery');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['enable_rush_delivery', 'rush_delivery_options']);
        });
    }
};
