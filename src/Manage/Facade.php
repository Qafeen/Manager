<?php
namespace Qafeen\Manager\Manage;

use hanneskod\classtools\Iterator\ClassIterator;
use Qafeen\Manager\Traits\Helper;
use Symfony\Component\Finder\Finder;

/**
 * Manage Service Provider
 *
 * @package Qafeen\Manager
 * @author Mohammed Mudasir <hello@mudasir.me>
 */
class Facade
{
    use Helper;

    /**
     * @var \Symfony\Component\Finder\Finder
     */
    protected $finder;

    /**
     * @var \Illuminate\Console\Command
     */
    protected $console;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $facades;

    protected $registered = false;

    /**
     * Facade constructor.
     *
     * @param  \Symfony\Component\Finder\Finder  $finder
     * @param          $console
     */
    public function __construct(Finder $finder, $console)
    {
        $this->finder  = $finder;

        $this->console = $console;
    }

    public function isRegistered()
    {
        return $this->registered;
    }

    /**
     * Search package by given name
     *
     * @return array
     */
    public function search()
    {
        $this->console->info("Searching directory for facades(aliases).");

        $facades = $this->getFacades();

        if (! $facades->count()) {
            $this->console->warn("No facades file found. Nothing to install.");

            return [];
        }

        $this->console->line(
            " Found {$facades->count()} facade" . ($facades->count() > 1 ? 's': '') . '.'
        );

        $facades->each(function($facade, $index) {
            $currentCount = $index + 1;

            $this->console->line(" $currentCount. $facade");
        });

        if (! $this->console->confirm("Register facades?", true)) {
            return [];
        }

        $this->registered = true;

        return $this->getFacades()->toArray();
    }

    /**
     * Get Facades list from the package
     *
     * @return \Illuminate\Support\Collection
     */
    public function getFacades()
    {
        if ($this->facades) {
            return $this->facades;
        }

        $facades = new ClassIterator($this->finder->contains('/class [A-Z]\w+ extends Facade/i'));

        // ClassIterator will give you facades, class name as key and path as value
        // we only need class name for now
        return $this->facades = collect(array_keys($facades->getClassMap()));
    }

    /**
     * Get facades count
     *
     * @return int
     */
    public function count()
    {
        return $this->facades->count();
    }
}
