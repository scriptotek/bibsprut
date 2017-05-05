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
            $table->increments('id');

            $table->string('youtube_id')->unique()->nullable();
            $table->json('youtube_meta')->nullable();  // or binary...

            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            //$table->string('duration')->nullable();
            //$table->string('filename')->nullable();
            //$table->integer('width')->nullable();
            //$table->integer('height')->nullable();

            $table->string('account_id')->nullable();

            $table->softDeletes();

            // $table->string('broadcast_status');
            // $table->string('language', 6)->nullable();
            // $table->string('title');
            // $table->text('description');
            // $table->json('tags');
            // $table->integer('views')->unsigned();
            // $table->string('thumbnail')->nullable();
            // $table->string('duration')->nullable();
            // $table->integer('filesize')->nullable();
            // $table->datetime('published_at');  // the date the youtube event was created, not broadcasted
            // $table->boolean('is_public');
            // $table->string('license')->nullable();
            // $table->integer('presentation_id')->unsigned()->nullable();
            // $table->integer('presentation_id')->unsigned()->nullable();
            $table->integer('vortex_event_id')->unsigned()->nullable();

            $table->timestamps();

//            $table->foreign('presentation_id')
//                ->references('id')->on('presentations')
//                ->onDelete('set null');

            $table->foreign('vortex_event_id')
                ->references('id')->on('vortex_events')
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
        Schema::drop('youtube_videos');
    }
}
