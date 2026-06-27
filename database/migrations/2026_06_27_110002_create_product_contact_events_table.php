<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_contact_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('viewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['phone_view', 'call_click', 'message_click']);
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['product_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_contact_events');
    }
};
