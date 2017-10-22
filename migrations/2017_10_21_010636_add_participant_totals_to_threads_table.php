<?php

use Cmgmyr\Messenger\Models\Models;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;

class AddParticipantTotalsToThreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(Models::table('threads'), function (Blueprint $table) {
            $table->integer('total_participants')->default(0)->after('subject');
        });

        if (App::environment() != 'testing') {
            foreach (Models::thread()->withTrashed()->get() as $thread) {
                $thread->updateParticipantsCount();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(Models::table('threads'), function (Blueprint $table) {
            $table->dropColumn('total_participants');
        });
    }
}
