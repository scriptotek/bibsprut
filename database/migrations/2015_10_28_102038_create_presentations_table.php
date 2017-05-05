<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePresentationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('presentations', function(Blueprint $table) {
            $table->increments('id');

            $table->string('title');
            $table->text('description');

            $table->time('start_time');
            $table->time('end_time');

            $table->json('tags');
            $table->string('thumbnail');

            $table->string('archive_dir');

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
        Schema::drop('presentations');
    }
}
