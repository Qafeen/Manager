<?php

use Illuminate\Support\ServiceProvider;

class DummyServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->publishes([
            realpath(__DIR__) => config_path('dummy'),
        ], 'config');
    }
}
