<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddNullableToLastReadInParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `'.DB::getTablePrefix().'participants` CHANGE COLUMN `last_read` `last_read` timestamp NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP;');
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
