<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerservicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Previously it was customerservices
        Schema::create('customer_services', function (Blueprint $table) {
            $table->id('cs_id');
            $table->string('cs_name');
            $table->string('cs_email');
            $table->string('password'); // previouesly it was cs_password
            $table->string('cs_resettoken')->nullable();
            $table->string('cs_mobileno');
            $table->string('cs_phone');
            $table->string('cs_street');
            $table->string('cs_city');
            $table->string('cs_state');
            $table->string('cs_postcode');
            $table->string('cs_image');
            $table->enum('is_verified', ['online', 'offline'])->default('offline');
            $table->string('bank_pass_no', 250)->nullable();
            $table->string('bank_pass_front', 250)->nullable();
            $table->string('bank_pass_back', 250)->nullable();
            $table->string('statement_conduct', 250)->nullable();
            $table->string('licence_front', 250)->nullable();
            $table->string('licence_back', 250)->nullable();
            $table->string('franchise_contract', 250)->nullable();
            $table->string('extra_option', 250)->nullable();
            $table->string('payroll_contract', 250)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('customer_services', function (Blueprint $table) {
            $table->renameColumn('cs_id', 'id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_services');
    }
}
