<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entity_relations', function (Blueprint $table) {
            $table->increments('id');
            $table->text('label')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        $default_role_id = DB::table('entity_relations')->insertGetId([
            'label' => 'Keyword',
            'description' => 'A generic keyword.',
            'created_at' => new DateTime(),
            'updated_at' => new DateTime(),
        ]);

        Schema::create('entities', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->text('entity_label')->unique();
            $table->text('entity_type')->default('Keyword');
            $table->json('entity_data')->nullable();
        });

        Schema::create('entity_youtube_video', function (Blueprint $table) use ($default_role_id) {
            $table->integer('youtube_video_id')->unsigned();
            $table->integer('entity_id')->unsigned();
            $table->integer('entity_relationship_id')
                ->unsigned()
                ->default($default_role_id);

            $table->foreign('entity_relationship_id')
                ->references('id')->on('entity_relations');

            $table->foreign('entity_id')
                ->references('id')->on('entities')
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
        Schema::dropIfExists('entity_youtube_video');
        Schema::dropIfExists('entities');
        Schema::dropIfExists('entity_relations');
    }
}
