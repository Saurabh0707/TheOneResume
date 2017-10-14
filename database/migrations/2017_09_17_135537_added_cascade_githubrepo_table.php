<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddedCascadeGithubrepoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('githubrepos', function (Blueprint $table) 
        {
          $table->dropForeign('githubrepos_githubuser_id_foreign');          
          $table->foreign('githubuser_id')->references('id')->on('githubusers')->onDelete('cascade');
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
