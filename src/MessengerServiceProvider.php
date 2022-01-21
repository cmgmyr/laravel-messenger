<?php

namespace Cmgmyr\Messenger;

use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Models;
use Cmgmyr\Messenger\Models\Participant;
use Cmgmyr\Messenger\Models\Thread;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;

class MessengerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function boot()
    {
        $this->offerPublishing();
        $this->setMessengerModels();
        $this->setUserModel();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->configure();
    }

    /**
     * Set up the configuration for Messenger.
     *
     * @return void
     */
    protected function configure()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/config.php',
            'messenger'
        );
    }

    /**
     * Set up the resource publishing groups for Messenger.
     *
     * @return void
     */
    protected function offerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('messenger.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../migrations' => base_path('database/migrations'),
            ], 'migrations');
        }
    }

    /**
     * Define Messenger's models in registry.
     *
     * @return void
     * @throws BindingResolutionException
     */
    protected function setMessengerModels()
    {
        $config = $this->app->make('config');

        Models::setMessageModel($config->get('messenger.message_model', Message::class));
        Models::setThreadModel($config->get('messenger.thread_model', Thread::class));
        Models::setParticipantModel($config->get('messenger.participant_model', Participant::class));

        Models::setTables([
            'messages' => $config->get('messenger.messages_table', Models::message()->getTable()),
            'participants' => $config->get('messenger.participants_table', Models::participant()->getTable()),
            'threads' => $config->get('messenger.threads_table', Models::thread()->getTable()),
        ]);
    }

    /**
     * Define User model in Messenger's model registry.
     *
     * @return void
     * @throws BindingResolutionException
     */
    protected function setUserModel()
    {
        $config = $this->app->make('config');

        $model = $config->get('messenger.user_model', function () use ($config) {
            return $config->get('auth.providers.users.model', $config->get('auth.model'));
        });

        Models::setUserModel($model);

        Models::setTables([
            'users' => (new $model())->getTable(),
        ]);
    }
}
