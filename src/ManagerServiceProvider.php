<?php
namespace Qafeen\Manager;

use Illuminate\Support\ServiceProvider;
use Qafeen\Manager\Console\Install;

/**
 * Manager Service Provider.
 *
 * @author Mohammed Mudasir <hello@mudasir.me>
 */
class ManagerServiceProvider extends ServiceProvider
{
    /**
     * Commands which need to be registered.
     * @var array
     */
    protected $commands = [
        Install::class,
    ];

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }
}
