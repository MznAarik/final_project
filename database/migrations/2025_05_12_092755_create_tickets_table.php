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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->unsignedBigInteger('event_id')->nullable();
            $table->foreign('event_id')
                ->references('id')
                ->on('events')
                ->onDelete('set null');

            $table->string('status', 20)->nullable()->default('pending');
            $table->decimal('price', 10, 2)->nullable();
            $table->unsignedBigInteger('quantity')->nullable()->default(1);
            $table->decimal('total_price', 10, 2)->nullable();
            $table->dateTime('deadline')->nullable();
            $table->string('cancellation_reason', 255)->nullable();
            $table->string('qr_code', 255)->nullable(); // For QR code storage
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->boolean('delete_flag')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
