<?php

namespace Qafeen\Manager\Manage;

use Symfony\Component\Finder\Finder;

/**
 * Manage Service Provider.
 *
 * @author Mohammed Mudasir <hello@mudasir.me>
 */
class Facade extends Manage
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $facades;

    /**
     * Is file registered in config/manger.php.
     *
     * @return bool
     */
    public function isRegistered()
    {
        return $this->registered;
    }

    /**
     * Search package by given name.
     *
     * @return array
     */
    public function search()
    {
        $this->console->info('Searching directory for facades(aliases).');

        $facades = $this->getFacades();

        if (!$facades->count()) {
            $this->console->warn('No facades file found. Nothing to install.');

            return [];
        }

        $this->console->line(
            " Found {$facades->count()} facade".($facades->count() > 1 ? 's' : '').'.'
        );

        $facades->each(function ($facade, $index) {
            $currentCount = $index + 1;

            $this->console->line(" $currentCount. $facade");
        });

        if (!$this->console->confirm('Register facades?', true)) {
            return [];
        }

        $this->registered = true;

        return $this->getFacades()->toArray();
    }

    /**
     * Get Facades list from the package.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getFacades()
    {
        return $this->facades ?: $this->facades = $this->getFileClasses(
                    $this->finder->contains('/class [A-Z]\w+ extends Facade/i')
                );
    }

    /**
     * Get facades count.
     *
     * @return int
     */
    public function count()
    {
        return $this->facades->count();
    }
}
