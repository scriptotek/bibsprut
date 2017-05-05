<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVortexEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vortex_events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url')->unique();
            $table->text('title');
            $table->json('organizers');
            $table->json('tags');
            $table->text('introduction')->nullable();
            $table->longText('text')->nullable();

            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();

            $table->string('location')->nullable();
            $table->string('location_map_url')->nullable();

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
        Schema::dropIfExists('vortex_events');
    }
}
