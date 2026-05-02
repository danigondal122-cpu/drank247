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
        Schema::create('customers', function (Blueprint $table) {
            $table->id('customer_id');
            $table->string('google_id', 250)->nullable();
            $table->string('social_login_id')->nullable();
            $table->enum('login_type', ['NORMAL', 'FACEBOOK', 'GOOGLE', 'CHANNEL', 'UBER', 'TAKEAWAY'])->default('NORMAL');
            $table->string('customer_resettoken')->nullable();
            $table->integer('customer_type')->comment('0->personal,1->business');
            $table->enum('customer_from', ['0', '1', '2', '3', '4'])->comment("'0'=>from web app,'1'=>'deliverect','2'=>'Customer App','3' => 'Uber EATS','4' => 'Takeaway'")->default('0');
            $table->text('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone', 250)->nullable();
            $table->string('password', 100)->nullable();
            $table->string('profile', 250)->nullable();
            $table->string('phone_code', 20)->nullable();
            $table->string('customer_contactno')->nullable();
            $table->string('customer_address')->nullable();
            $table->string('customer_devicetoken', 250)->nullable();
            $table->string('customer_device', 250)->nullable();
            $table->string('customer_hash', 250)->nullable();
            $table->enum('is_verified', ['FALSE', 'TRUE'])->default('FALSE');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->renameColumn('customer_id', 'id');
            $table->renameColumn('customer_resettoken', 'customer_reset_token');
            $table->renameColumn('customer_contactno', 'customer_contact_no');
            $table->renameColumn('customer_devicetoken', 'customer_device_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
};
