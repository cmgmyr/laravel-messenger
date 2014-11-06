<?php namespace Cmgmyr\Messenger\Tests;

date_default_timezone_set('America/New_York');

use Illuminate\Database\Capsule\Manager as DB;
use AdamWathan\Faktory\Faktory;

class TestCase extends \PHPUnit_Framework_TestCase
{
    protected $faktory;

    public function setUp()
    {
        $this->configureDatabase();
        $this->migrateTables();
        $this->faktory = new Faktory;
        $load_factories = function ($faktory) {
            require(__DIR__ . '/factories.php');
        };
        $load_factories($this->faktory);
    }

    private function configureDatabase()
    {
        $db = new DB;
        $db->addConnection(
            [
                'driver'    => 'sqlite',
                'database'  => ':memory:',
                'charset'   => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix'    => '',
            ]
        );

        $db->bootEloquent();
        $db->setAsGlobal();
    }

    private function migrateTables()
    {
        $this->createUsersTable();
        $this->createThreadsTable();
        $this->createMessagesTable();
        $this->createParticipantsTable();
    }

    private function createUsersTable()
    {
        DB::schema()->create(
            'users',
            function ($table) {
                $table->increments('id');
                $table->string('first_name');
                $table->string('last_name');
                $table->string('email')->unique();
                $table->enum('notify', ['y', 'n'])->default('y');
                $table->timestamps();
            }
        );
    }

    private function createThreadsTable()
    {
        DB::schema()->create(
            'threads',
            function ($table) {
                $table->increments('id');
                $table->string('subject');
                $table->timestamps();
            }
        );
    }

    private function createMessagesTable()
    {
        DB::schema()->create(
            'messages',
            function ($table) {
                $table->increments('id');
                $table->integer('thread_id')->unsigned();
                $table->integer('user_id')->unsigned();
                $table->text('body');
                $table->timestamps();
            }
        );
    }

    private function createParticipantsTable()
    {
        DB::schema()->create(
            'participants',
            function ($table) {
                $table->increments('id');
                $table->integer('thread_id')->unsigned();
                $table->integer('user_id')->unsigned();
                $table->timestamp('last_read');
                $table->timestamps();
                $table->softDeletes();
            }
        );
    }
}
