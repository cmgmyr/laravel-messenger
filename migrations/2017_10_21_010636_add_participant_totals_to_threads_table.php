<?php

use Cmgmyr\Messenger\Models\Thread;
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
        Schema::table('threads', function (Blueprint $table) {
            $table->integer('total_participants')->default(0)->after('subject');
        });

        if (App::environment() != 'testing') {
            foreach (Thread::withTrashed()->get() as $thread) {
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
        Schema::table('threads', function (Blueprint $table) {
            $table->dropColumn('total_participants');
        });
    }
}
