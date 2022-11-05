<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterDietsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_diets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
			$table->unsignedBigInteger('trainer_id');
			$table->foreign('trainer_id')->references('id')->on('users');
			$table->enum('type', ['Vegetarian', 'Non Vegetarian', 'Eggetarian']);
            $table->unsignedBigInteger('level_id');
            $table->string('file_path');
            $table->text('description')->nullable();
			$table->softDeletes();
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
        Schema::dropIfExists('master_diets');
    }
}
