<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('location');
            $table->string('venue', 100);
            $table->integer('capacity');
            $table->json('ticket_pricing')->after('ticket_price')->nullable();
            $table->text('description')->nullable();
            $table->string('contact_info', 100)->nullable();
            $table->timestamp('start_date')->nullable(false);
            $table->timestamp('end_date')->nullable();
            $table->string('category', 50);
            $table->string('status', 50)->default('Upcoming');
            $table->string('organizer', 100);
            $table->string('img_path', 255)->nullable();
            $table->integer('tickets_sold')->nullable()->default(0);
            $table->string('currency', 5)->default('NPR');
            $table->foreignId('district_id')->nullable()->constrained('districts')->onDelete('set null');
            $table->foreignId('province_id')->nullable()->constrained('provinces')->onDelete('set null');
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('set null');
            $table->index(['district_id', 'province_id', 'country_id'], 'events_location_index');
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->boolean('delete_flag')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
