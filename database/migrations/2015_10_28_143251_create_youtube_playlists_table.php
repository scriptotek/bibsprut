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

            $table->timestamps();

        });

        Schema::table('events', function(Blueprint $table) {
            $table->integer('youtube_playlist_id')->unsigned()->nullable();
            $table->foreign('youtube_playlist_id')
                ->references('id')->on('youtube_playlists')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function(Blueprint $table) {
            $table->dropForeign('events_youtube_playlist_id_foreign');
            $table->dropColumn('youtube_playlist_id');
        });
        Schema::drop('youtube_playlists');
    }
}
