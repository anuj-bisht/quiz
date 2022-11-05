<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamResultTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('exam_id')->nullable();
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('question_id')->nullable();
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('option_id')->nullable();
            $table->foreign('option_id')->references('id')->on('questions')->onDelete('cascade')->onUpdate('cascade');
            $table->string('ans_id',10)->default('Null');
            $table->string('is_correct',10)->default('Null');
            $table->string('is_attempted',10)->default('Null');
            $table->string('marks',10)->default('Null');
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
        Schema::dropIfExists('exam_result');
    }
}
