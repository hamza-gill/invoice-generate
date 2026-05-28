<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'default_template_id')) {
                $table->foreignId('default_template_id')->nullable()->constrained('invoice_templates')->nullOnDelete();
            }
            if (!Schema::hasColumn('settings', 'custom_invoice_css')) {
                $table->longText('custom_invoice_css')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'default_template_id')) {
                $table->dropConstrainedForeignId('default_template_id');
            }
            if (Schema::hasColumn('settings', 'custom_invoice_css')) {
                $table->dropColumn('custom_invoice_css');
            }
        });
    }
};
