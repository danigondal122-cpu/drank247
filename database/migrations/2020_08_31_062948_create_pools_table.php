<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pools', function (Blueprint $table) {
            $table->id('pool_id');
            $table->string('from_postcode', 100);
            $table->string('to_postcode', 100);
            $table->string('area', 100);
            $table->string('delivery_charge');
            $table->string('delivery_startfrom');
            $table->string('delivery_freefrom');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('pools', function (Blueprint $table) {
            $table->renameColumn('pool_id', 'id');
            $table->renameColumn('delivery_startfrom', 'delivery_start_from');
            $table->renameColumn('delivery_freefrom', 'delivery_free_from');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pools');
    }
};
