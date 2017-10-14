<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddedCascadeGithubUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('githubusers', function (Blueprint $table) 
        {
          $table->dropForeign('githubusers_user_id_foreign');
          $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');      
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('githubusers');
    }
}
