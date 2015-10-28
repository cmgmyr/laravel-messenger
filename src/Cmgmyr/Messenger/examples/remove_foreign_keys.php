<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RemoveForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign('messages_thread_id_foreign');
            $table->dropForeign('messages_user_id_foreign');
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->dropForeign('participants_thread_id_foreign');
            $table->dropForeign('participants_user_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
