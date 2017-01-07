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
class ServiceProvider
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
    protected $providers;

    /**
     * ServiceProvider constructor.
     *
     * @param  \Symfony\Component\Finder\Finder  $finder
     * @param  \Illuminate\Console\Command  $console
     */
    public function __construct(Finder $finder, $console)
    {
        $this->finder  = $finder;

        $this->console = $console;
    }

    /**
     * Start searching service provider in package.
     *
     * @return array
     */
    public function search()
    {
        $this->console->info("Searching directory for service providers.");

        $sps = $this->getProviders();

        if (! $sps->count()) {
            $this->console->warn("No service provider file found. Nothing to install.");

            return [];
        }

        $this->console->line(
            " Found {$sps->count()} Service provider" . ($sps->count() > 1 ? 's': '') . '.'
        );

        $sps->each(function($sp, $index) {
            $currentCount = $index + 1;

            $this->console->line(" $currentCount. $sp");
        });

        if (! $this->console->confirm("Register service providers?", true)) {
            return [];
        }

        return $this->getProviders()->toArray();
    }

    /**
     * Get service providers
     *
     * @return \Illuminate\Support\Collection
     */
    public function getProviders()
    {
        if ($this->providers) {
            return $this->providers;
        }

        $providers = new ClassIterator(
            $this->finder->contains('/class [A-Z]\w+ extends ServiceProvider/i')
        );

        // ClassIterator will give you providers, class name as key and path as value
        // we only need class name for now
        return $this->providers = collect(array_keys($providers->getClassMap()));
    }
}
