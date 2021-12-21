<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code',50)->unique();
            $table->string('coupon_code',50)->nullable();
            $table->integer('coupon_amount')->default(0);
            $table->integer('enhancement')->default(0);
            $table->integer('shipping')->default(0);
            $table->double('subtotal')->default(0);
            $table->integer('order_qty')->default(0);
            $table->double('order_price')->default(0);
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->datetime('birthday')->nullable();
            $table->text('note')->nullable();
            $table->text('invoice')->nullable();
            $table->integer('district_id')->default(0);
            $table->integer('province_id')->default(0);
            $table->integer('payment_id')->default(0);
            $table->integer('member_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('status_id')->default(1);
            $table->integer('priority')->default(1);
            $table->string('status',100)->default('publish');
            $table->string('type',50)->nullable();
            $table->softDeletes()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
