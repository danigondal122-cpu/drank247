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
        Schema::create('franchise_pool', function (Blueprint $table) {
            $table->foreignId('franchise_id')->constrained();
            $table->foreignId('pool_id')->constrained();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['pool_id', 'franchise_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('franchise_pool');
    }
};
