<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'invoice_template_id')) {
                $table->foreignId('invoice_template_id')->nullable()->after('organization_id')->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('invoices', 'custom_fields')) {
                $table->json('custom_fields')->nullable()->after('note');
            }
            if (!Schema::hasColumn('invoices', 'recurring_invoice_id')) {
                $table->foreignId('recurring_invoice_id')->nullable()->after('invoice_template_id')->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('invoices', 'estimate_id')) {
                $table->unsignedBigInteger('estimate_id')->nullable()->after('recurring_invoice_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'invoice_template_id')) {
                $table->dropConstrainedForeignId('invoice_template_id');
            }
            if (Schema::hasColumn('invoices', 'custom_fields')) {
                $table->dropColumn('custom_fields');
            }
            if (Schema::hasColumn('invoices', 'recurring_invoice_id')) {
                $table->dropConstrainedForeignId('recurring_invoice_id');
            }
            if (Schema::hasColumn('invoices', 'estimate_id')) {
                $table->dropColumn('estimate_id');
            }
        });
    }
};
