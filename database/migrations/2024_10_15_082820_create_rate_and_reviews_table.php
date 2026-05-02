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
        // Previously it was rate_review
        Schema::create('rate_and_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('dp_id');
            $table->string('rate', 11);
            $table->text('review');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['order_id']);
        });

        Schema::table('rate_and_reviews', function (Blueprint $table) {
            $table->renameColumn('dp_id', 'delivery_person_id');

            $table->foreign('delivery_person_id')->references('id')->on('delivery_people');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_and_reviews');
    }
};
