<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWmsTransferDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wms_transfer_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id');
            $table->string('product_code',50);
            $table->string('product_title')->nullable();
            $table->integer('product_qty')->default(0);
            $table->double('product_price')->default(0);
            $table->integer('size_id')->default(0);
            $table->string('size_title')->nullable();
            $table->integer('color_id')->default(0);
            $table->string('color_title')->nullable();
            $table->integer('unit')->default(0);
            $table->integer('transfer_id')->unsigned();
            $table->foreign('transfer_id')->references('id')->on('wms_transfers')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('wms_transfer_details');
    }
}
