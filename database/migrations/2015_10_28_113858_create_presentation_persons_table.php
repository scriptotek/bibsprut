<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePresentationPersonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('presentation_persons', function(Blueprint $table) {
            $table->increments('id');

            $table->integer('presentation_id')->unsigned();
            $table->integer('person_id')->unsigned();

            $table->string('role');

            $table->foreign('presentation_id')
                ->references('id')->on('presentations')
                ->onDelete('cascade');

            $table->foreign('person_id')
                ->references('id')->on('persons')
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
        Schema::drop('presentation_persons');
    }
}
