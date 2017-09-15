<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRepobranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repobranches', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('githubrepo_id')->unsigned();            
            $table->string('name');
            $table->timestamps();
        });
        Schema::table('repobranches', function (Blueprint $table) 
        {
          $table->foreign('githubrepo_id')->references('id')->on('githubrepos');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('repobranches');
    }
}
