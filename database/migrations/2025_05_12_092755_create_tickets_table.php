<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('event_id');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->string('batch_code')->nullable();
            $table->string('qr_code')->nullable();
            $table->json('ticket_details')->nullable();
            $table->decimal('total_price', 10, 2)->nullable();
            $table->unsignedInteger('total_quantity')->nullable()->after('ticket_details');
            $table->string('status')->default('pending');
            $table->dateTime('deadline')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->boolean('delete_flag')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }

}
