<?php

namespace Qafeen\Manager;

use hanneskod\classtools\Iterator\ClassIterator;
use Symfony\Component\Finder\Finder;

/**
 * ManageServiceProvider.php.
 *
 * @author Mohammed Mudasir <hello@mudasir.me>
 */
class ManageServiceProvider
{
    protected $finder;

    protected $console;

    public function __construct(Finder $finder, $console)
    {
        $this->finder = $finder;

        $this->console = $console;
    }

    public static function instance(Finder $finder, $console)
    {
        return (new static($finder, $console));
    }

    public function search()
    {
        $sps = $this->getServiceProviders();

        if (! $sps->count()) {
            $this->console->info("No service provider file found. Nothing to install.");

            return false;
        }

        $this->console->info(
            "Found {$sps->count()} Service provider" . ($sps->count() > 1 ? 's': '') . '.'
        );

        $sps->each(function($sp, $index) {
            $currentCount = $index + 1;

            $this->console->info("  $currentCount.] $sp");
        });
    }

    public function getServiceProviders()
    {
        $providers = new ClassIterator($this->finder->contains('ServiceProvider'));

        // ClassIterator will give you providers, class name as key and path as value
        // we only need class name for now
        return collect(array_keys($providers->getClassMap()));
    }
}
