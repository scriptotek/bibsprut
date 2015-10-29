<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYoutubeVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('youtube_videos', function(Blueprint $table) {
            //$collection->unique(array('vocabulary', 'identifier'));
            $table->increments('id');
            $table->string('youtube_id')->unique();
            $table->string('title');
            $table->text('description');
            $table->jsonb('tags');
            $table->string('thumbnail');
            $table->datetime('published_at');
            $table->boolean('is_public');
            $table->string('license');
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
        Schema::drop('youtube_videos');
    }

}
