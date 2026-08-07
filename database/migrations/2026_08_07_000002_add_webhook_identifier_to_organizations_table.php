<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('webhook_identifier', 64)->nullable()->unique()->after('slug');
        });

        // Backfill existing organizations with a unique identifier
        $organizations = DB::table('organizations')->whereNull('webhook_identifier')->get(['id']);
        foreach ($organizations as $organization) {
            DB::table('organizations')
                ->where('id', $organization->id)
                ->update(['webhook_identifier' => (string) Str::uuid()]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropUnique(['webhook_identifier']);
            $table->dropColumn('webhook_identifier');
        });
    }
};
