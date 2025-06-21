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
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('category', 50)->nullable(false)->after('status');
            $table->decimal('price', 10, 2)->after('status');
            $table->string('description', 255)->after('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('category');
            $table->dropColumn('price');
            $table->dropColumn('description');
        });
    }
};
