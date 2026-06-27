<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE products MODIFY COLUMN description TEXT');
        DB::statement('ALTER TABLE products MODIFY COLUMN type_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE products MODIFY COLUMN color_id BIGINT UNSIGNED NULL');

        Schema::table('products', function (Blueprint $table) {
            $table->string('breed')->nullable()->after('name');
            $table->string('gender', 20)->nullable()->after('breed');
            $table->string('health_status')->nullable()->after('gender');
            $table->string('contact_phone', 50)->nullable()->after('health_status');
            $table->string('location')->nullable()->after('city_id');
            $table->decimal('latitude', 10, 8)->nullable()->after('location');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->unsignedInteger('views_count')->default(0)->after('longitude');
            $table->json('images')->nullable()->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['breed', 'gender', 'health_status', 'contact_phone',
                'location', 'latitude', 'longitude', 'views_count', 'images']);
        });

        DB::statement('ALTER TABLE products MODIFY COLUMN description VARCHAR(255)');
        DB::statement('ALTER TABLE products MODIFY COLUMN type_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE products MODIFY COLUMN color_id BIGINT UNSIGNED NOT NULL');
    }
};
