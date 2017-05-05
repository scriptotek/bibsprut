<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoogleAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_accounts', function (Blueprint $table) {
            $table->string('id')->nullable()->unique();
            $table->text('channel')->nullable();
            $table->json('token')->nullable();
            $table->json('userinfo')->nullable();
            $table->integer('token_expiration')->nullable()->unsigned();
            $table->timestamps();

            $table->primary('id');
        });

        Schema::table('youtube_videos', function(Blueprint $table) {
            $table->foreign('account_id')
                ->references('id')->on('google_accounts')
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
        Schema::table('youtube_videos', function(Blueprint $table) {
            $table->dropForeign('youtube_videos_account_id_foreign');
        });
        Schema::dropIfExists('google_accounts');
    }
}
