<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id');
            $table->string('product_code',50);
            $table->string('product_title')->nullable();
            $table->integer('product_qty')->default(0);
            $table->double('product_price')->default(0);
            $table->double('product_price_second')->default(0);
            $table->integer('size_id')->default(0);
            $table->string('size_title')->nullable();
            $table->integer('color_id')->default(0);
            $table->string('color_title')->nullable();
            $table->integer('order_id')->unsigned();
            $table->foreign('order_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('cascade');
            $table->string('status',50)->default('publish');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_details');
    }
}
