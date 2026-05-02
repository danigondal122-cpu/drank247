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
        Schema::create('pool_sub_delivery_person', function (Blueprint $table) {
            $table->foreignId('pool_id')->constrained();
            $table->foreignId('sub_delivery_person_id')->constrained();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['pool_id', 'sub_delivery_person_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pool_sub_delivery_person');
    }
};
