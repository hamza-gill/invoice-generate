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
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('subject');
            $table->string('priority')->default('medium'); // low | medium | high | urgent
            $table->string('status')->default('open');     // open | in_progress | resolved | closed
            $table->unsignedBigInteger('opened_by')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->boolean('is_read_by_admin')->default(false);
            $table->boolean('is_read_by_org')->default(true);
            $table->timestamps();

            $table->index(['organization_id', 'status']);
            $table->index('priority');
        });

        Schema::create('support_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('support_ticket_id')->constrained()->cascadeOnDelete();
            $table->string('sender_type'); // admin | user
            $table->unsignedBigInteger('sender_id');
            $table->text('body');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['support_ticket_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_messages');
        Schema::dropIfExists('support_tickets');
    }
};
