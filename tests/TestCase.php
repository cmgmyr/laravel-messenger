<?php

namespace Cmgmyr\Messenger\Tests;

use AdamWathan\Faktory\Faktory;
use Cmgmyr\Messenger\MessengerServiceProvider;
use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Cmgmyr\Messenger\Models\Thread;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    use RefreshDatabase;

    /**
     * @var \AdamWathan\Faktory\Faktory
     */
    protected $faktory;

    protected $loadEnvironmentVariables = true;

    public function setUp(): void
    {
        parent::setUp();

        $this->faktory = new Faktory;
        $load_factories = static function ($faktory) {
            require __DIR__ . '/factories.php';
        };
        $load_factories($this->faktory);

        Eloquent::unguard();
        $this->ensureUsersTable($this->app);
        $this->seedUsersTable();
    }

    protected function getApplicationTimezone($app): string
    {
        return 'America/New_York';
    }

    protected function getPackageProviders($app): array
    {
        return [
            MessengerServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations(): void
    {
//        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(__DIR__ . '/../migrations');
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => env('DB_CONNECTION', 'sqlite'),
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT'),
            'database' => env('DB_DATABASE', ':memory:'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
        ]);

        $app['config']->set('messenger.user_model', User::class);
        $app['config']->set('messenger.message_model', Message::class);
        $app['config']->set('messenger.participant_model', Participant::class);
        $app['config']->set('messenger.thread_model', Thread::class);
    }

    private function ensureUsersTable($app): void
    {
        if (!$app['db']->connection()->getSchemaBuilder()->hasTable('users')) {
            $app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        }
    }

    private function seedUsersTable(): void
    {
        $this->addUser([
            'name' => 'Chris Gmyr',
            'email' => 'chris@test.com',
        ]);

        $this->addUser([
            'name' => 'Adam Wathan',
            'email' => 'adam@test.com',
        ]);

        $this->addUser([
            'name' => 'Taylor Otwell',
            'email' => 'taylor@test.com',
        ]);
    }

    protected function addUser(array $overrides = []): User
    {
        /*
         * retry() is a "hack" to get the MySQL action to be less flaky
         * because sometimes the users table isn't available.
         */
        return retry(5, static function () use ($overrides) {
            return User::create(array_merge([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            ], $overrides));
        }, 1000);
    }
}
