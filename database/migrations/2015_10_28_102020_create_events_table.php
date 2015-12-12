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
            $table->string('uuid')->unique();

            $table->string('title');
            $table->text('intro');
            $table->text('description');
            $table->jsonb('tags');
            $table->string('thumbnail');
            $table->date('start_date');
            $table->string('archive_dir');

            $table->string('twitter_hashtag');

            $table->string('vortex_url')->nullable();
            $table->string('facebook_id')->nullable();

            $table->string('location')->nullable();
            $table->string('location_map_url')->nullable();

            $table->dateTime('unpublished_at');

            $table->timestamps();
            $table->softDeletes();
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
