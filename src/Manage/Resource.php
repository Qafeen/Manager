<?php

namespace Qafeen\Manager\Manage;

/**
 * Manage Resource.
 *
 * @author Mohammed Mudasir <hello@mudasir.me>
 */
class Resource extends File
{
    /**
     * Publish resource files.
     *
     * @param string $provider package service provider
     *
     * @return bool|int
     */
    public function publish($provider)
    {
        $publishCommand = 'vendor:publish';
        $this->console->info('Searching directory for vue and blade files.');

        // Get the blade and vue files count.
        $this->count = $this->finder->name('*.blade.php')->name('*.vue')->count();

        if ($this->count == 0) {
            $this->console->warn('No blade, vue or config file found.');

            return true;
        }

        $tag = $this->console->ask("If the \"{$this->console->tokenizePackageInfo()['name']}\" has specify vendor publish tag in installation guide then please add it here or press enter to skip adding tag.", false);

        $this->registered = true;

        return $this->console->call($publishCommand, [
            '--provider' => $provider,
            '--tag'      => $tag,
        ]);
    }

    /**
     * Get the total resource files count.
     *
     * @return int
     */
    public function count()
    {
        return $this->count;
    }
}
