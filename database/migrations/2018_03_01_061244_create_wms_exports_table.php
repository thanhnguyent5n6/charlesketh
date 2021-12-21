<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWmsExportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wms_exports', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code',50)->unique();
            $table->string('store_code',50)->nullable();
            $table->integer('export_qty')->default(0);
            $table->double('export_price')->default(0);
            $table->string('note_cancel')->nullable();
            $table->integer('order_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();
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
        Schema::dropIfExists('wms_exports');
    }
}
