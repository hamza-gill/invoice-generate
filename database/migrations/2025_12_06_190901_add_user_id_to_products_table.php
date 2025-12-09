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
        Schema::table('products', function (Blueprint $table) {
            // Add user_id (not nullable unless you want it to be)
            $table->unsignedBigInteger('user_id')->nullable()->after('id');

            // Foreign key reference
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade'); // delete products when user is deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
