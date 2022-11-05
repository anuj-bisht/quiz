<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->integer('project_id'); 
            $table->integer('brand_id');             
            $table->integer('model_id');                             
            $table->string('chainage');                             
            $table->integer('uom_id');                             
            $table->enum('status', ['open', 'in-progress','done']);
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
        Schema::dropIfExists('equipments');
    }
}
