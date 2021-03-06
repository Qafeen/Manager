<?php

namespace Qafeen\Manager;

use Illuminate\Support\ServiceProvider;
use Qafeen\Manager\Console\Add;

/**
 * Manager Service Provider.
 *
 * @author Mohammed Mudasir <hello@mudasir.me>
 */
class ManagerServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerByConfig();

        if ($this->app->runningInConsole()) {
            $this->commands([
                Add::class,
            ]);
        }
    }

    public function registerByConfig()
    {
        $config = config('manager');

        if (isset($config['providers']) and is_array($config['providers'])) {
            foreach ($config['providers'] as $provider) {
                $this->app->register($provider);
            }
        }

        if (isset($config['aliases']) and is_array($config['aliases'])) {
            foreach ($config['aliases'] as $name => $class) {
                $this->app->alias($class, $name);
            }
        }
    }
}
