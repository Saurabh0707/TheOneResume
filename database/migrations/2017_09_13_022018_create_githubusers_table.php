<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGithubusersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('githubusers', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('user_id')->unsigned();                        
            $table->string('username');
            $table->string('html_url');
            $table->string('name');
            $table->string('company');
            $table->string('location');
            $table->dateTimeTz('user_created_at');
            $table->dateTimeTz('user_updated_at');
            $table->unsignedInteger('public_repos');
            $table->unsignedInteger('public_gists');
            $table->timestamps();
        });
        Schema::table('githubusers', function (Blueprint $table) 
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
        Schema::dropIfExists('githubuser');
    }
}
