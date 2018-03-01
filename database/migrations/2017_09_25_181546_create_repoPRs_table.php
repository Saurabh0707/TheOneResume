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
        Schema::create('repo_p_rs', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('githubrepo_id')->unsigned();
            $table->integer('open_issues')->unsigned();
            $table->string('state'); 
            $table->string('title'); 
            $table->string('body'); 
            $table->string('assignee'); 
            $table->string('creator');
            $table->string('pr_created_at', 50);
            $table->string('pr_updated_at', 50);
            $table->string('pr_closed_at', 50);
            $table->string('pr_merged_at', 50);
            $table->timestamps();
        });
        Schema::table('repo_p_rs', function (Blueprint $table) 
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
