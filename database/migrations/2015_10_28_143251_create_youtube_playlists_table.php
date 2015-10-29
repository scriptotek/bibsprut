<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYoutubePlaylistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('youtube_playlists', function(Blueprint $table) {
            $table->increments('id');
            $table->string('youtube_id')->unique();

            $table->string('title');
            $table->text('description');
            $table->boolean('is_public');
            $table->integer('presentation_id')->unsigned()->nullable();

            $table->timestamps();

            $table->foreign('presentation_id')
                ->references('id')->on('presentations');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('youtube_playlists');
    }
}
