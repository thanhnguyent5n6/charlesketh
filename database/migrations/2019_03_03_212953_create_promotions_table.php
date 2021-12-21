<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->double('coupon_amount')->default(0);
            $table->string('category_id')->nullable();
            $table->string('product_id')->nullable();
            $table->string('product_limit')->nullable();
            $table->string('change_conditions_type')->nullable();
            $table->timestamp('begin_at')->nullable();
            $table->timestamp('end_at')->nullable();
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
        Schema::dropIfExists('promotions');
    }
}
