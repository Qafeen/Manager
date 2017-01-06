<?php
namespace Qafeen\Manager\Manage;

use hanneskod\classtools\Iterator\ClassIterator;
use Symfony\Component\Finder\Finder;

/**
 * Manage Service Provider
 *
 * @author Mohammed Mudasir <hello@mudasir.me>
 */
class ServiceProvider
{
    protected $finder;

    protected $console;

    protected $providers;

    public function __construct(Finder $finder, $console)
    {
        $this->finder = $finder;

        $this->console = $console;
    }

    public static function instance(Finder $finder, $console)
    {
        return (new static($finder, $console));
    }

    public function register()
    {
        if (! $this->getProviders()->count()) {
            $this->console->warn("No service provider file found. Nothing to install.");

            return false;
        }

        $sps = $this->getProviders();

        $this->console->line(
            " Found {$sps->count()} Service provider" . ($sps->count() > 1 ? 's': '') . '.'
        );

        $sps->each(function($sp, $index) {
            $currentCount = $index + 1;

            $this->console->line(" $currentCount. $sp");
        });

        if ($this->console->confirm("Register it?", true)) {

        }

        return true;
    }

    public function getProviders()
    {
        if ($this->providers) {
            return $this->providers;
        }

        $providers = new ClassIterator($this->finder->contains('ServiceProvider'));

        // ClassIterator will give you providers, class name as key and path as value
        // we only need class name for now
        return $this->providers = collect(array_keys($providers->getClassMap()));
    }
}
