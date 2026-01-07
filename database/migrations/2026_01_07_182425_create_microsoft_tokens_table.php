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
        Schema::create('microsoft_tokens', function (Blueprint $table) {
            $table->id();
            $table->text('access_token', 2048);
            $table->text('refresh_token', 2048)->nullable();
            $table->integer('expires_in'); // seconds
            $table->timestamp('expires_at'); // actual datetime
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('microsoft_tokens');
    }
};
