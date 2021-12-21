<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWmsInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wms_inventories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('store_code')->nullable();
            $table->integer('supplier_id')->unsigned()->nullable();
            $table->integer('product_id');
            $table->string('product_code');
            $table->string('product_title')->nullable();
            $table->integer('product_qty')->default(0);
            $table->double('product_price')->default(0);
            $table->integer('unit')->default(0);
            $table->integer('import')->default(0);
            $table->integer('export')->default(0);
            $table->integer('inventory')->default(0);
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
        Schema::dropIfExists('wms_inventories');
    }
}
