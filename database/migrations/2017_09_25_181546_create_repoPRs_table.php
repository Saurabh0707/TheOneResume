<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRepoPRsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repoPRs', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('githubrepo_id')->unsigned();
            $table->integer('open_issues')->unsigned();
            $table->string('state'); 
            $table->string('title'); 
            $table->string('body'); 
            $table->string('assignee'); 
            $table->string('creator');
            $table->dateTimeTz('pr_created_at');
            $table->dateTimeTz('pr_updated_at');
            $table->dateTimeTz('pr_closed_at');
            $table->dateTimeTz('pr_merged_at');
            $table->timestamps();
        });
        Schema::table('repoPRs', function (Blueprint $table) 
        {
          $table->foreign('githubrepo_id')->references('id')->on('githubrepos')->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('repoPRs');
    }
}