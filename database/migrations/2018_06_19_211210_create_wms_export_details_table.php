<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWmsExportDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wms_export_details', function (Blueprint $table) {
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
            $table->integer('export_id')->unsigned();
            $table->foreign('export_id')->references('id')->on('wms_exports')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('wms_export_details');
    }
}
