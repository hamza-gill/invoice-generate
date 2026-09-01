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
            $table->boolean('enable_invoice_reminders')->default(false)->after('webhook_secret');
            $table->json('reminder_schedule')->nullable()->after('enable_invoice_reminders');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedTinyInteger('reminder_level')->default(0)->after('estimate_id');
            $table->timestamp('last_reminder_sent_at')->nullable()->after('reminder_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['enable_invoice_reminders', 'reminder_schedule']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['reminder_level', 'last_reminder_sent_at']);
        });
    }
};
