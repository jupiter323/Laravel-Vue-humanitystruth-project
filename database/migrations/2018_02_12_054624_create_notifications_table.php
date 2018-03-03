<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id');
        	//this account will receive notification from additional actions to specified investigation, topic, file or comment.
        	$table->integer('account_id');
        
        	$table->integer('topic_id');
        	$table->integer('investigation_id');
			$table->integer('file_id');
        	$table->integer('comment_id');
        	
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
