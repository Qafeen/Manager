<?php

namespace Qafeen\Manager\Manage;

/**
 * Manage Service Provider.
 *
 * @author Mohammed Mudasir <hello@mudasir.me>
 */
class ServiceProvider extends Manage
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $providers;

    /**
     * Start searching service provider in package.
     *
     * @return array
     */
    public function search()
    {
        $this->console->info('Searching directory for service providers.');

        $sps = $this->getProviders();

        if (!$sps->count()) {
            $this->console->warn('No service provider file found. Nothing to install.');

            return [];
        }

        $this->console->line(
            " Found {$sps->count()} Service provider".($sps->count() > 1 ? 's' : '').'.'
        );

        $sps->each(function ($sp, $index) {
            $currentCount = $index + 1;

            $this->console->line(" $currentCount. $sp");
        });

        if (!$this->console->confirm('Register service providers?', true)) {
            return [];
        }

        $this->registered = true;

        return $this->getProviders()->toArray();
    }

    /**
     * Get service providers.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getProviders()
    {
        if ($this->providers) {
            return $this->providers;
        }

        return $this->providers = $this->getFileClasses(
            $this->finder->contains('/class [A-Z]\w+ extends ServiceProvider/i')
        );
    }

    /**
     * Get service providers count.
     *
     * @return int
     */
    public function count()
    {
        return $this->providers->count();
    }
}
