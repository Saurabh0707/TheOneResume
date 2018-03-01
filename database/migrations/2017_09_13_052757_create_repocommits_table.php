<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRepocommitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repocommits', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('githubrepo_id')->unsigned();
            $table->string('author');
            $table->string('committer');
            $table->string('message');
            $table->string('commit_created_at', 50);
            $table->string('commit_updated_at', 50);
            $table->timestamps();
        });
        Schema::table('repocommits', function (Blueprint $table) 
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
        Schema::dropIfExists('repocommits');
    }
}
