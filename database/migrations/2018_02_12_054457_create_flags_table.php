<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flags', function (Blueprint $table) {
            $table->increments('id');
        	$table->integer('account_id');
        	//$table->enum('reason', { 'unethical','irrelevant','other' } );
        
        	//flag a specific topic, investigation, or comment
        	$table->integer('investigation_id');
        	$table->integer('topic_id');
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
        Schema::dropIfExists('flags');
    }
}
