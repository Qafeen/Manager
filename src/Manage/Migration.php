<?php
namespace Qafeen\Manager\Manage;

use hanneskod\classtools\Iterator\ClassIterator;
use Qafeen\Manager\ProcessBuilder;
use Qafeen\Manager\Traits\Helper;
use Symfony\Component\Finder\Finder;

/**
 * Manage Migration
 *
 * @package Qafeen\Manager
 * @author Mohammed Mudasir <hello@mudasir.me>
 */
class Migration
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

    protected $hasMigrationFile;

    /**
     * Is file registered.
     * @var bool
     */
    protected $registered = false;

    /**
     * Count of migration files
     * @var int
     */
    protected $count = 0;

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
     * Search package by given name
     *
     * @return bool
     */
    public function search()
    {
        $this->console->info("Searching directory for Migrations.");

        return $this->hasMigrationFile() ? $this->console->confirm("Run migrations?", true): false;
    }

    /**
     *
     * Get Migration file list from the package
     *
     * @return bool
     */
    public function hasMigrationFile()
    {
        $migrations = new ClassIterator($this->finder->contains('/class [A-Z]\w+ extends Migration/i'));

        $this->count = count($migrations->getClassMap());

        return $this->hasMigrationFile = $this->count > 0;
    }

    public function count()
    {
        return $this->count;
    }
}
