<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoogleAccounts extends Migration
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
            $table->binary('channel')->nullable();
            $table->binary('token')->nullable();
            $table->binary('userinfo')->nullable();
            $table->integer('token_expiration')->nullable()->unsigned();
            $table->timestamps();

            $table->primary('id');
        });

        Schema::table('recordings', function(Blueprint $table) {
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
        Schema::table('recordings', function(Blueprint $table) {
            $table->dropForeign('recordings_account_id_foreign');
        });
        Schema::dropIfExists('google_accounts');
    }
}
