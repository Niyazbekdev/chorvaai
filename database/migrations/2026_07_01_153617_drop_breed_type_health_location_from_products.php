<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['type_id']);
            $table->dropColumn(['type_id', 'breed', 'health_status', 'location']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('type_id')->nullable()->constrained('types')->onDelete('cascade');
            $table->string('breed')->nullable()->after('name');
            $table->string('health_status')->nullable();
            $table->string('location')->nullable();
        });
    }
};
