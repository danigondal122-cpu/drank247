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
        // Previously it was customerservice_hours
        Schema::create('customer_service_hours', function (Blueprint $table) {
            $table->id('h_id');
            $table->integer('cs_id')->default(1);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('start_time');
            $table->string('end_time');
            $table->string('per_hours');
            $table->string('per_hours_inM');
            $table->string('total_hours_inM');
            $table->string('total_hours_inH');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('customer_service_hours', function (Blueprint $table) {
            $table->renameColumn('h_id', 'id');
            $table->renameColumn('cs_id', 'customer_service_id');
            $table->renameColumn('per_hours_inM', 'per_hours_in_minute');
            $table->renameColumn('total_hours_inM', 'total_hours_in_minute');
            $table->renameColumn('total_hours_inH', 'total_hours_in_hour');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_service_hours');
    }
};
