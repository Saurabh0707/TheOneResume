<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('works', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned(); 
            $table->string('position'); 
            $table->string('location'); 
            $table->string('organisation'); 
            $table->string('description');
            $table->date('from'); 
            $table->date('to'); 
            $table->timestamps();
        });
        Schema::table('works', function (Blueprint $table) 
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
        Schema::dropIfExists('works');
    }
}
