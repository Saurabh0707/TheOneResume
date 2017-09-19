<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddedCascadeRepocontributorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('repocontributors', function (Blueprint $table) 
        {
           $table->dropForeign('repocontributors_githubrepo_id_foreign');     
           $table->foreign('githubrepo_id')->references('id')->on('githubrepos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {        
        Schema::dropIfExists('repocontributors');
    }
}
