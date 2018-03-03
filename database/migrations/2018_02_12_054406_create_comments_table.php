<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
        	$table->timestamp('timestamp');
        	$table->text('content');
        
        	//comment on a specific account, topic, investigation, or comment
        	$table->integer('account_id');
        	$table->integer('topic_id');
        	$table->integer('investigation_id');
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
        Schema::dropIfExists('comments');
    }
}
