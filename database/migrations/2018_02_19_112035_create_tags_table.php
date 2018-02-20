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
        Schema::create('tag_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->text('label')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        $default_role_id = DB::table('tag_roles')->insertGetId([
            'label' => 'Subject',
            'description' => 'A topic the talk/video is about.',
            'created_at' => new DateTime(),
            'updated_at' => new DateTime(),
        ]);

        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->text('tag_name')->unique();
            $table->text('tag_type')->default('Keyword');
            $table->json('tag_data')->nullable();
        });

        Schema::create('tag_youtube_video', function (Blueprint $table) use ($default_role_id) {
            $table->integer('youtube_video_id')->unsigned();
            $table->integer('tag_id')->unsigned();
            $table->integer('tag_role_id')
                ->unsigned()
                ->default($default_role_id);

            $table->foreign('tag_role_id')
                ->references('id')->on('tag_roles');

            $table->foreign('tag_id')
                ->references('id')->on('tags')
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
        Schema::dropIfExists('tag_youtube_video');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('tag_roles');
    }
}
