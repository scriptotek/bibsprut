<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYoutubePlaylistVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('youtube_playlist_videos', function(Blueprint $table) {
            $table->increments('id');

            $table->integer('youtube_playlist_id')->unsigned();
            $table->integer('youtube_video_id')->unsigned();

            $table->string('playlist_position');

            $table->foreign('youtube_playlist_id')
                ->references('id')->on('youtube_playlists')
                ->onDelete('cascade');

            $table->foreign('youtube_video_id')
                ->references('id')->on('youtube_videos')
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
        Schema::drop('youtube_playlist_videos');
    }
}
