<?php

use Cmgmyr\Messenger\Models\Models;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignToMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(Models::table('messages'), function (Blueprint $table) {
            $table->foreign('thread_id')->references('id')->on(Models::table('threads'));
            $table->foreign('user_id')->references('id')->on(Models::table('users'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(Models::table('messages'), function (Blueprint $table) {
            $table->dropForeign(['thread_id']);
            $table->dropForeign(['user_id']);
        });
    }
}
