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
        Schema::table('events', function (Blueprint $table) {
            $table->json('ticket_pricing')->after('ticket_price')->nullable();
            $table->dropColumn('ticket_price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('ticket_pricing');
            $table->decimal('ticket_price', 8, 2)->after('ticket_category')->nullable();
        });
    }
};
