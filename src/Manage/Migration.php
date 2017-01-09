<?php

namespace Qafeen\Manager\Manage;

/**
 * Manage Migration.
 *
 * @author Mohammed Mudasir <hello@mudasir.me>
 */
class Migration extends Manage
{
    /**
     * @var bool
     */
    protected $hasMigrationFile;

    /**
     * Run the migration command.
     *
     * @return bool|int
     */
    public function run()
    {
        if ($this->search()) {
            $this->registered = true;

            return $this->console->call('migrate');
        }

        return false;
    }

    /**
     * Search package by given name.
     *
     * @return bool
     */
    public function search()
    {
        $this->console->info('Searching directory for Migrations.');

        return $this->hasMigrationFile() ? $this->console->confirm('Run migrations?', true) : false;
    }

    /**
     * Get Migration file list from the package.
     *
     * @return bool
     */
    public function hasMigrationFile()
    {
        $this->count = $this->getFileClasses($this->finder->contains('/class [A-Z]\w+ extends Migration/i'))
                            ->count();

        return $this->hasMigrationFile = $this->count > 0;
    }

    public function count()
    {
        return $this->count;
    }
}
