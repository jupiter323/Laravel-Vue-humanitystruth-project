<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('value')->unique();
        });
        
        Schema::create('investigation_tag', function (Blueprint $table) {
            $table->integer('investigation_id');
            $table->integer('tag_id');
            $table->primary(['investigation_id', 'tag_id']);
        });
        
        Schema::create('file_tag', function (Blueprint $table) {
            $table->integer('file_id');
            $table->integer('tag_id');
            $table->primary(['file_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tags');
        
        Schema::dropIfExists('investigation_tag');
        
        Schema::dropIfExists('file_tag');
    }
}
