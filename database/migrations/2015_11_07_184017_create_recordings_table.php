<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecordingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recordings', function(Blueprint $table) {
            $table->increments('id');

            $table->string('youtube_id')->unique()->nullable();
            $table->jsonb('youtube_meta')->nullable();  // or binary...

            $table->date('recorded_at')->nullable();
            $table->string('duration')->nullable();
            $table->string('filename')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();

            // $table->string('broadcast_status');
            // $table->string('language', 6)->nullable();
            // $table->string('title');
            // $table->text('description');
            // $table->jsonb('tags');
            // $table->integer('views')->unsigned();
            // $table->string('thumbnail')->nullable();
            // $table->string('duration')->nullable();
            // $table->integer('filesize')->nullable();
            // $table->datetime('published_at');  // the date the youtube event was created, not broadcasted
            // $table->boolean('is_public');
            // $table->string('license')->nullable();
            // $table->integer('presentation_id')->unsigned()->nullable();
            $table->integer('presentation_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('presentation_id')
                ->references('id')->on('presentations')
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
        Schema::drop('recordings');
    }
}
