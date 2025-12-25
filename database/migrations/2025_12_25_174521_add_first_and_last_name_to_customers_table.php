<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('stripe_customer_id');
            $table->string('last_name')->nullable()->after('first_name');

            // Drop old name column
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Restore name column
            $table->string('name')->nullable();

            // Remove first & last name
            $table->dropColumn(['first_name', 'last_name']);
        });
    }
};
