<?php

namespace Qafeen\Manager\Manage;

use hanneskod\classtools\Iterator\ClassIterator;
use Qafeen\Manager\Traits\Helper;
use Symfony\Component\Finder\Finder;

/**
 * Manage.php.
 *
 * @author Mohammed Mudasir <hello@mudasir.me>
 */
class Manage
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
     * Is file registered.
     *
     * @var bool
     */
    protected $registered = false;

    /**
     * Count of migration files.
     *
     * @var int
     */
    protected $count = 0;

    /**
     * File registered and migration ran successfully.
     *
     * @return bool
     */
    public function isRegistered()
    {
        return $this->registered;
    }

    /**
     * Facade constructor.
     *
     * @param \Symfony\Component\Finder\Finder $finder
     * @param \Qafeen\Manager\Console\Add      $console
     */
    public function __construct(Finder $finder, $console)
    {
        $this->finder = $finder;

        $this->console = $console;
    }

    /**
     * Get classes from given files.
     *
     * @param Finder $finder
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getFileClasses(Finder $finder)
    {
        $files = (new ClassIterator($finder))->getClassMap();

        // We only need class name which is stored as key
        return collect(array_keys($files));
    }
}
