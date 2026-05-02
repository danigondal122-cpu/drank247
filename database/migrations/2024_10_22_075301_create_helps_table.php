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
        Schema::dropIfExists('helps');
        // Previously it was help
        Schema::create('helps', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type')->comment("'0'=>'Customer Services','1'=>'Franchise'");
            $table->foreignId('to_id');
            $table->foreignId('d_id');
            $table->foreignId('order_id')->constrained();
            $table->text('message');
            $table->integer('status')->default(9);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('helps', function (Blueprint $table) {
            $table->renameColumn('d_id', 'delivery_person_id');
            $table->renameColumn('status', 'order_status_id');

            $table->foreign('delivery_person_id')->references('id')->on('delivery_people');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('helps');
    }
};
