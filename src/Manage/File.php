<?php

namespace Qafeen\Manager\Manage;

use hanneskod\classtools\Iterator\ClassIterator;
use Qafeen\Manager\Traits\Helper;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * Manage.php.
 *
 * @author Mohammed Mudasir <hello@mudasir.me>
 */
abstract class File
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
     * @var int|null
     */
    protected $count = null;

    /**
     * @var \hanneskod\classtools\Iterator\SplFileInfo[]
     */
    protected $files;

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
     * @param  $contains
     *
     * @return \Qafeen\Manager\Manage\File
     */
    public function fileHas($contains)
    {
        $this->files = (new ClassIterator($this->finder->contains($contains)))->getClassMap();

        $this->count = count($this->files);

        return $this;
    }

    /**
     * Get classes from files.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getClasses()
    {
        return collect(array_keys($this->files));
    }

    /**
     * Get files.
     *
     * @return \hanneskod\classtools\Iterator\SplFileInfo[]
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Get the file by class name.
     *
     * @param $class
     *
     * @throws \Symfony\Component\HttpFoundation\File\Exception\FileException;
     *
     * @return \hanneskod\classtools\Iterator\SplFileInfo
     */
    public function getFile($class)
    {
        if (is_null($this->count)) {
            return $this->fileHas($class)->getFile($class);
        }

        if (isset($this->files[$class])) {
            return $this->files[$class];
        }

        throw new FileException("File does not exists with given `{$class}` class.");
    }
}
