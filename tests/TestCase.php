<?php namespace Cmgmyr\Messenger\tests;

date_default_timezone_set('America/New_York');

use Illuminate\Database\Capsule\Manager as DB;
use AdamWathan\Faktory\Faktory;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \AdamWathan\Faktory\Faktory
     */
    protected $faktory;

    /**
     * Set up the database, migrations, and initial data
     */
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

    /**
     * Configure the database
     */
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

    /**
     * Run the migrations for the database
     */
    private function migrateTables()
    {
        $this->createUsersTable();
        $this->createThreadsTable();
        $this->createMessagesTable();
        $this->createParticipantsTable();

        $this->seedUsersTable();
    }

    /**
     * Create the users table in the database
     */
    private function createUsersTable()
    {
        DB::schema()->create(
            'users',
            function ($table) {
                $table->increments('id');
                $table->string('name');
                $table->string('email')->unique();
                $table->enum('notify', ['y', 'n'])->default('y');
                $table->timestamps();
            }
        );
    }

    /**
     * Create some users for the tests to use
     */
    private function seedUsersTable()
    {
        DB::insert('INSERT INTO ' . DB::getTablePrefix() . 'users (id, name, email, created_at, updated_at) VALUES (?, ?, ?, datetime(), datetime())', [1, 'Chris Gmyr', 'chris@test.com']);
        DB::insert('INSERT INTO ' . DB::getTablePrefix() . 'users (id, name, email, created_at, updated_at) VALUES (?, ?, ?, datetime(), datetime())', [2, 'Adam Wathan', 'adam@test.com']);
        DB::insert('INSERT INTO ' . DB::getTablePrefix() . 'users (id, name, email, created_at, updated_at) VALUES (?, ?, ?, datetime(), datetime())', [3, 'Taylor Otwell', 'taylor@test.com']);
    }

    /**
     * Create the threads table in the database
     */
    private function createThreadsTable()
    {
        DB::schema()->create(
            'threads',
            function ($table) {
                $table->increments('id');
                $table->string('subject');
                $table->timestamps();
                $table->softDeletes();
            }
        );
    }

    /**
     * Create the messages table in the database
     */
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

    /**
     * Create the participants table in the database
     */
    private function createParticipantsTable()
    {
        DB::schema()->create(
            'participants',
            function ($table) {
                $table->increments('id');
                $table->integer('thread_id')->unsigned();
                $table->integer('user_id')->unsigned();
                $table->timestamp('last_read')->nullable();
                $table->timestamps();
                $table->softDeletes();
            }
        );
    }
}
