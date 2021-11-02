<?php

namespace Cmgmyr\Messenger\Tests;

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

    protected $loadEnvironmentVariables = true;

    public function setUp(): void
    {
        parent::setUp();
        Eloquent::unguard();
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

    protected function seedUsersTable(): void
    {
        $this->userFactory([
            'name' => 'Chris Gmyr',
            'email' => 'chris@test.com',
        ]);

        $this->userFactory([
            'name' => 'Adam Wathan',
            'email' => 'adam@test.com',
        ]);

        $this->userFactory([
            'name' => 'Taylor Otwell',
            'email' => 'taylor@test.com',
        ]);
    }

    protected function userFactory(array $overrides = []): User
    {
        return User::create(array_merge([
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        ], $overrides));
    }

    protected function threadFactory(array $overrides = []): Thread
    {
        return Thread::create(array_merge([
            'subject' => 'Sample thread',
        ], $overrides));
    }

    protected function messageFactory(array $overrides = []): Message
    {
        return Message::create(array_merge([
            'user_id' => 1,
            'thread_id' => 1,
            'body' => 'A message',
        ], $overrides));
    }

    protected function participantFactory(array $overrides = []): Participant
    {
        return Participant::create(array_merge([
            'user_id' => 1,
            'thread_id' => 1,
        ], $overrides));
    }
}
