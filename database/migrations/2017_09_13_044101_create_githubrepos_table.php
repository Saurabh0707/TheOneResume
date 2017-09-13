<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGithubreposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('githubrepos', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('githubuser_id')->unsigned();            
            $table->string('owner');
            $table->string('name');
            $table->string('html_url');
            $table->string('clone_url');
            $table->dateTimeTz('repo_created_at');
            $table->dateTimeTz('repo_updated_at');
            $table->dateTimeTz('repo_pushed_at');
            $table->unsignedInteger('public_repos');
            $table->unsignedInteger('no_of_commits');
            $table->unsignedInteger('no_of_branches');
            $table->unsignedInteger('no_of_pullrequests');
            $table->unsignedInteger('no_of_contributors');
        });
        Schema::table('githubrepos', function (Blueprint $table) 
        {
          $table->foreign('githubuser_id')->references('id')->on('githubusers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('githubrepos');
    }
}
