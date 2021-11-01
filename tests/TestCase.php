<?php

namespace Cmgmyr\Messenger\Tests;

use AdamWathan\Faktory\Faktory;
use Cmgmyr\Messenger\MessengerServiceProvider;
use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Cmgmyr\Messenger\Models\Thread;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    use RefreshDatabase, WithFaker;

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
        $this->loadMigrationsFrom(__DIR__ . '/../tests/database');
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

    private function seedUsersTable(): void
    {
        $this->addUser([
            'id' => 1,
            'name' => 'Chris Gmyr',
            'email' => 'chris@test.com',
        ]);

        $this->addUser([
            'id' => 2,
            'name' => 'Adam Wathan',
            'email' => 'adam@test.com',
        ]);

        $this->addUser([
            'id' => 3,
            'name' => 'Taylor Otwell',
            'email' => 'taylor@test.com',
        ]);
    }

    protected function addUser(array $overrides = []): User
    {
        return User::create(array_merge([
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        ], $overrides));
    }
}
