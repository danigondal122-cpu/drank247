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
        // Previously it was deliveryperson
        Schema::create('delivery_people', function (Blueprint $table) {
            $table->id('dp_id');
            $table->string('dp_name');
            $table->string('dp_email');
            $table->string('dp_password');
            $table->string('dp_contactno');
            $table->string('dp_street');
            $table->string('dp_city');
            $table->string('dp_state');
            $table->string('dp_postcode');
            $table->string('dp_image')->nullable();
            $table->string('dp_device', 250)->nullable();
            $table->string('dp_devicetoken', 250)->nullable();
            $table->string('dp_hash', 250)->nullable();
            $table->string('dp_lat', 250)->nullable();
            $table->string('dp_lng', 250)->nullable();
            $table->enum('dp_onoff', ['online', 'offline'])->default('offline');
            $table->string('dp_startodometer_number', 250);
            $table->string('dp_stopodometer_number', 250);
            $table->integer('history_id')->default(0); // TODO!: Belum tahu fungsinya apa. Sementara set default 0 supaya bisa di create
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

        Schema::table('delivery_people', function (Blueprint $table) {
            $table->renameColumn('dp_id', 'id');
            $table->renameColumn('dp_contactno', 'dp_contact_no');
            $table->renameColumn('dp_devicetoken', 'dp_device_token');
            $table->renameColumn('dp_startodometer_number', 'dp_start_odometer_number');
            $table->renameColumn('dp_stopodometer_number', 'dp_stop_odometer_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_people');
    }
};
