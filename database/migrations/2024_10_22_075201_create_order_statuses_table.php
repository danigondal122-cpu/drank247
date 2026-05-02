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
        // TODO: Replace this migration with PHP Enum
        // Previously it was order_status
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->id('os_id');
            $table->string('os_name', 100);
            $table->string('os_color', 10);
            $table->timestamps();
        });

        Schema::table('order_statuses', function (Blueprint $table) {
            $table->renameColumn('os_id', 'id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_statuses');
    }
};
