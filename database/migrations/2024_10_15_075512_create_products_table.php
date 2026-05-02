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
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->string('product_name');
            $table->string('product_articleNumber')->nullable();
            $table->tinyInteger('product_type')->comment('"0"=>main product,"1"=>extraproduct');
            $table->string('image');
            $table->decimal('product_price', 10, 2);
            $table->integer('vat');
            $table->decimal('vat_price', 10, 2);
            $table->foreignId('category_id')->nullable()->constrained();
            $table->text('description')->nullable();
            // $table->integer('min_stock');
            // $table->string('current_stock');
            // $table->string('is_reminder_set');
            $table->string('alcohol', 25)->nullable();
            $table->foreignId('order_from')->comment('0=>stock api,1=>Other wholesale company,2=>247Drank owe warehouse'); // or other warehouse?
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_show')->default(false);
            // $table->boolean('active_status')->default(1);
            $table->unsignedBigInteger('product_order')->default(0);
            $table->integer('api_availablestock')->default(0);
            $table->string('main_price')->nullable();
            $table->decimal('drank247_price', 10, 2)->nullable();
            $table->decimal('customer_price', 10, 2)->nullable();
            $table->decimal('franchise_price', 10, 2)->nullable();
            $table->integer('alcoholic_items');
            $table->foreignId('uber_product_type');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('product_id', 'id');
            $table->renameColumn('product_articleNumber', 'product_article_number');
            $table->renameColumn('api_availablestock', 'api_available_stock');
            $table->renameColumn('uber_product_type', 'product_type_id');

            $table->foreign('product_type_id')->references('id')->on('product_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
