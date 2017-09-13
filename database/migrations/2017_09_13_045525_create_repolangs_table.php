<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRepolangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repolangs', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('githubrepo_id')->unsigned();            
            $table->string('name');
            $table->unsignedInteger('lines');
        });
        Schema::table('repolangs', function (Blueprint $table) 
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
        Schema::dropIfExists('repolangs');
    }
}
