<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code',20)->unique();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->double('coupon_amount')->default(0);
            $table->integer('number_of_uses')->default(0);
            $table->double('min_restriction_amount')->default(0);
            $table->double('max_restriction_amount')->default(0);
            $table->string('change_conditions_type')->default(0);
            $table->timestamp('begin_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->integer('priority')->default(1);
            $table->string('status',100)->default('publish');
            $table->string('type',50)->nullable();
            $table->integer('used')->default(0);
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
        Schema::dropIfExists('coupons');
    }
}
