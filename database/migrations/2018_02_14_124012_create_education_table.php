<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEducationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('education', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned(); 
            $table->string('college'); 
            $table->string('degree'); 
            $table->string('stream'); 
            $table->string('type'); 
            $table->date('from'); 
            $table->date('to');
            $table->decimal('marks', 5, 2); 
            $table->timestamps();
        });
     Schema::table('education', function (Blueprint $table) 
        {
          $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('education');
    }
}