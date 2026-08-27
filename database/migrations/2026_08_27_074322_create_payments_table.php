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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('type', 20);              // subscription | banner
            $table->unsignedInteger('amount');        // UZS (e.g. 50000)
            $table->string('provider', 20);           // payme | click
            $table->string('status', 20)->default('pending'); // pending|paid|failed|cancelled

            // Provider transaction ID (Payme transactionId / Click click_trans_id)
            $table->string('provider_transaction_id')->nullable()->index();

            // Payme: create_time, perform_time, cancel_time, reason. Click: extra fields.
            $table->json('meta')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
