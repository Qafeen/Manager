<?php
namespace Qafeen\Manager\Manage;

use hanneskod\classtools\Iterator\ClassIterator;
use Qafeen\Manager\Traits\Helper;
use Symfony\Component\Finder\Finder;

/**
 * Manage Service Provider
 *
 * @author Mohammed Mudasir <hello@mudasir.me>
 */
class Facade
{
    use Helper;

    protected $finder;

    protected $console;

    protected $facades;

    public function __construct(Finder $finder, $console)
    {
        $this->finder  = $finder;

        $this->console = $console;
    }

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

        return $this->getFacades()->toArray();
    }

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
}
