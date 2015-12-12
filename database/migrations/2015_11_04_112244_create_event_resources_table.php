<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_resources', function(Blueprint $table) {
            $table->increments('id');
            $table->string('original_url');
            $table->string('original_filename');
            $table->string('filename');
            $table->string('filetype');
            $table->string('mime');
            $table->integer('width');
            $table->integer('height');
            $table->string('role');
            $table->string('license');
            $table->string('attribution');
            $table->integer('event_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('event_id')
                ->references('id')->on('events')
                ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('event_resources');
    }
}
