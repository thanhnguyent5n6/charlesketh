<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->unique();
            $table->double('regular_price')->default(0);
            $table->double('sale_price')->default(0);
            $table->double('original_price')->default(0);
            $table->double('wholesale_price')->default(0);
            $table->integer('weight')->default(0);
            $table->string('link')->nullable();
            $table->string('image')->nullable();
            $table->string('alt')->nullable();
            $table->string('attachments')->nullable();
            $table->string('filters')->nullable();
            $table->integer('priority')->default(1);
            $table->string('status',100)->default('publish');
            
            $table->integer('supplier_id')->unsigned()->nullable();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onUpdate('cascade')->onDelete('cascade');

            $table->integer('category_id')->unsigned()->default(1);
            $table->foreign('category_id')->references('id')->on('categories')->onUpdate('cascade')->onDelete('cascade');

            $table->integer('user_id')->unsigned()->default(1);
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

            $table->string('type',50)->nullable();
            $table->integer('viewed')->default(0);
            $table->dateTime('expired_at')->nullable();
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
        Schema::dropIfExists('products');
    }
}
