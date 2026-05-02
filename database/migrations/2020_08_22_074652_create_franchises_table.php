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
        Schema::create('franchises', function (Blueprint $table) {
            $table->id('franchise_id');
            $table->string('franchises_name', 80);
            $table->string('franchises_no', 20);
            $table->string('image', 250)->nullable();
            $table->string('franchises_email', 100);
            $table->string('password');
            $table->string('reset_token')->nullable();
            $table->string('franchises_username', 100);
            $table->string('first_name', 30);
            $table->string('last_name', 30);
            $table->string('mobile_no', 100)->nullable();
            $table->string('date_of_birth', 50)->nullable();
            $table->string('company_name', 150)->nullable();
            $table->string('house_no_street', 150);
            $table->string('block_no', 30)->nullable();
            $table->string('post_code', 150);
            $table->string('residence', 100)->nullable();
            $table->string('landmark', 100)->nullable();
            $table->string('bank_account', 100)->nullable();
            // $table->text('franchise_pool');
            $table->decimal('per_day_charges', 8, 2);
            $table->string('royalty', 11);
            $table->string('start_from_date', 50)->nullable();
            $table->enum('fs_onoff', ['online', 'offline'])->default('online');
            $table->string('bank_pass_no', 250)->nullable();
            $table->string('bank_pass_front', 250)->nullable();
            $table->string('bank_pass_back', 250)->nullable();
            $table->string('statement_conduct', 250)->nullable();
            $table->string('licence_front', 250)->nullable();
            $table->string('licence_back', 250)->nullable();
            $table->string('franchise_contract', 250)->nullable();
            $table->string('extra_option', 250)->nullable();
            $table->integer('payroll_contract')->nullable();
            $table->string('franchise_number', 50)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('country', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('franchises', function (Blueprint $table) {
            $table->renameColumn('franchise_id', 'id');
            $table->renameColumn('fs_onoff', 'fs_on_off');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('franchises');
    }
};
