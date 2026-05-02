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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->uuid('order_uuid');
            $table->string('order_store_id')->nullable();
            $table->string('order_uber_id')->nullable();
            $table->string('order_uber_display_id')->nullable();
            $table->string('uber_order_delivery_type');
            $table->string('uber_order_delivery_status');
            $table->string('order_takeaway_id')->nullable();
            $table->string('order_takeaway_key')->nullable();
            $table->string('order_takeaway_public_ref')->nullable();
            $table->string('order_deliverect_id');
            $table->foreignId('order_channel_id')->nullable();
            $table->string('order_channelorder_id');
            $table->string('channel_link');
            $table->string('order_receiptid');
            $table->foreignId('order_franchiseid')->nullable();
            $table->foreignId('order_customerid');
            $table->boolean('order_approve')->comment('0->approve,1->approved');
            $table->foreignId('od_deliverypersonid')->nullable();
            $table->foreignId('order_addressid')->nullable();
            $table->text('order_note')->nullable();
            $table->string('order_price');
            $table->string('order_deliverycharge')->nullable();
            $table->string('order_servicecharge');
            $table->string('order_finalamount')->nullable();
            $table->string('order_discount');
            $table->string('order_finalwithdiscount');
            $table->foreignId('order_promocode')->nullable();
            $table->integer('order_status');
            $table->string('order_cancelledreason');
            $table->string('order_payment');
            $table->boolean('order_payment_status')->default(false)->comment('1=>YES,0=>NO');;
            $table->string('payment_method')->nullable();
            $table->string('service_fee')->nullable();
            $table->string('identity_entrance_code', 50);
            $table->string('identity_transaction_id');
            $table->string('qr_id')->nullable();
            $table->string('merchant_reference')->nullable();
            $table->integer('iban_entrance_code');
            $table->integer('iban_transaction_id');
            $table->dateTime('od_assignedtime')->nullable();
            $table->dateTime('od_starttime')->nullable();
            $table->dateTime('od_endtime')->nullable();
            $table->text('failed_reason')->nullable(false);
            $table->text('rejected_reason')->nullable(false);
            $table->string('od_rejectedid');
            $table->dateTime('order_deliverytime')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('order_id', 'id');
            $table->renameColumn('order_uuid', 'uuid');
            $table->renameColumn('order_channelorder_id', 'order_channel_order_id');
            $table->renameColumn('order_receiptid', 'order_receipt_id');
            $table->renameColumn('order_franchiseid', 'franchise_id');
            $table->renameColumn('order_channel_id', 'channel_id');
            $table->renameColumn('order_customerid', 'customer_id');
            $table->renameColumn('od_deliverypersonid', 'delivery_person_id');
            $table->renameColumn('order_addressid', 'order_address_id');
            $table->renameColumn('order_deliverycharge', 'order_delivery_charge');
            $table->renameColumn('order_servicecharge', 'order_service_charge');
            $table->renameColumn('order_finalamount', 'order_final_amount');
            $table->renameColumn('order_finalwithdiscount', 'order_final_with_discount');
            $table->renameColumn('order_promocode', 'promo_code_id');
            $table->renameColumn('order_cancelledreason', 'order_cancelled_reason');
            $table->renameColumn('od_assignedtime', 'od_assigned_time');
            $table->renameColumn('od_starttime', 'od_start_time');
            $table->renameColumn('od_endtime', 'od_end_time');
            $table->renameColumn('od_rejectedid', 'od_rejected_id');
            $table->renameColumn('order_deliverytime', 'order_delivery_time');

            $table->foreign('franchise_id')->references('id')->on('franchises');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('delivery_person_id')->references('id')->on('delivery_people');
            $table->foreign('promo_code_id')->references('id')->on('promo_codes');
            $table->foreign('channel_id')->references('id')->on('channels');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
