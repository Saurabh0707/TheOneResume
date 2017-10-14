<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddedCascadeRepolangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('repolangs', function (Blueprint $table) 
        {
           $table->dropForeign('repolangs_githubrepo_id_foreign');     
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
        Schema::dropIfExists('repolangs');
    }
}
