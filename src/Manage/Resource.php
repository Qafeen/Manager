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
        $class = last(explode('\\', $provider));

        $this->console->info("Searching {$provider} to publish vendor file.");

        if (!$this->finder->contains("/$class".'((.|\n)*)\$this->publishes\(/i')->count()) {
            $this->console->warn('Nothing to publish.');

            return true;
        }

        $tag = $this->console->ask("If the \"{$this->console->tokenizePackageInfo()['name']}\" has specify vendor publish tag in installation guide then please add it here or press enter to skip adding tag.", false);

        $this->console->call('vendor:publish', [
            '--provider' => $provider,
            '--tag'      => $tag,
        ]);

        return $this->registered = true;
    }
}
