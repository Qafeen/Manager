<?php

namespace Qafeen\Manager\Manage;

/**
 * Manage Resource.
 *
 * @author Mohammed Mudasir <hello@mudasir.me>
 */
class Resource extends Manage
{
    protected $resources;

    public function publish()
    {
        $this->console->info('Searching directory for vue and blade files.');

        $files = $this->getFileClasses(
            $this->finder->contains('/class [A-Z]\w+ extends Facade/i')
        )->count();

        if (!$files > 0) {
            return false;
        }

        if ($this->console->confirm('Publish resource files?')) {
            return $this->console->call('vendor:publish');
        }

        return false;
    }
}
