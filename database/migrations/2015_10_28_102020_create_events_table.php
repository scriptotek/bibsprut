<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function(Blueprint $table) {
            //$collection->unique(array('vocabulary', 'identifier'));
            $table->increments('id');

            $table->string('title');
            $table->text('description');
            $table->jsonb('tags');
            $table->string('thumbnail');

            $table->string('vortex_url')->nullable();
            $table->string('facebook_url')->nullable();

            $table->string('youtube_playlist_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('events');
    }
}
