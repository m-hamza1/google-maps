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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('place_id')->unique()->index();
            $table->string('name')->index();
            $table->string('primary_phone', 50)->nullable()->index();
            $table->string('secondary_phone', 50)->nullable(); // Mobile user editable
            $table->decimal('rating', 3, 1)->nullable();
            $table->unsignedInteger('reviews_count')->default(0);
            $table->text('address')->nullable();
            $table->string('category', 100)->nullable()->index();
            $table->text('website')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('notes')->nullable(); // Mobile user editable
            $table->string('status', 30)->default('new')->index(); // new, contacted, interested, not_interested, closed
            $table->string('search_query')->nullable()->index();
            $table->string('source', 50)->default('google_maps');
            $table->timestamps();
        });

        Schema::create('scrape_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('query');
            $table->string('location')->nullable();
            $table->string('status', 30)->default('pending'); // pending, processing, completed, failed
            $table->unsignedInteger('total_scraped')->default(0);
            $table->unsignedInteger('total_added')->default(0);
            $table->unsignedInteger('total_updated')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scrape_jobs');
        Schema::dropIfExists('leads');
    }
};
