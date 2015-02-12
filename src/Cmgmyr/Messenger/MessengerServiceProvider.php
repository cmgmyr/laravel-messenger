<?php namespace Cmgmyr\Messenger;

use Illuminate\Support\ServiceProvider;

class MessengerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            base_path('vendor/cmgmyr/messenger/src/config/config.php') => config_path('messenger.php'),
            base_path('vendor/cmgmyr/messenger/src/migrations') => base_path('database/migrations'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            base_path('vendor/cmgmyr/messenger/src/config/config.php'), 'messenger'
        );
    }
}
