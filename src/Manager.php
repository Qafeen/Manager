<?php

namespace Qafeen\Manager;

use ErrorException;
use Exception;
use Illuminate\Console\Command;
use Qafeen\Manager\Manage\ConfigFile;
use Qafeen\Manager\Manage\Facade;
use Qafeen\Manager\Manage\Migration;
use Qafeen\Manager\Manage\Resource;
use Qafeen\Manager\Manage\ServiceProvider;
use Qafeen\Manager\Traits\Helper;
use Symfony\Component\Finder\Finder;

/**
 * Package manager will handling installing, uninstalling or deleting packages.
 *
 * @author Mohammed Mudasir <hello@mudasir.me>
 */
class Manager
{
    use Helper;

    /**
     * Configuration detail of a given package.
     *
     * @var array
     */
    protected $config;

    /**
     * Name of a package.
     *
     * @var string
     */
    protected $name;

    /**
     * package directory.
     *
     * @var string
     */
    protected $directory;

    /**
     * List of service providers.
     *
     * @var \Qafeen\Manager\Manage\ServiceProvider
     */
    protected $providers;

    /**
     * @var \Qafeen\Manager\Manage\Facade
     */
    protected $facades;

    /**
     * @var \Qafeen\Manager\Manage\Migration
     */
    protected $migration;

    /**
     * @var \Qafeen\Manager\Manage\Resource
     */
    protected $resources;

    /**
     * Console class to notify user.
     *
     * @var \Qafeen\Manager\Console\Add
     */
    protected $console;

    /**
     * List of package files.
     *
     * @var \Symfony\Component\Finder\Finder
     */
    protected $files;

    /**
     * @param string $name
     * @param mixed  $console
     */
    public function __construct($name, $console)
    {
        $this->setName($name)
             ->setDirectory("vendor/$name/")
             ->setConsole($console);
    }

    public function build()
    {
        $providers = $this->getProviders()->search();
        $facades = $this->getFacades()->search();

        if (!ConfigFile::instance($providers, $facades)->make()) {
            throw new ErrorException('Unable to register providers and facades. 
                Please report this incident at Qafeen/Manager');
        }

        if (!$this->getResources()->publish()) {
            $this->console->warn('Unable to publish views files. Please report this incident at Qafeen/Manager');
        }

        return $this;
    }

    /**
     * Get Service Providers
     *
     * @return ServiceProvider
     */
    public function getProviders()
    {
        return $this->providers ?:
            $this->providers = new ServiceProvider(clone $this->getFiles(), $this->console);
    }

    /**
     * Get Facades
     *
     * @return Facade
     */
    public function getFacades()
    {
        return $this->facades ?:
            $this->facades = new Facade(clone $this->getFiles(), $this->console);
    }

    /**
     * Get migration
     *
     * @return Migration
     */
    public function getMigration()
    {
        return $this->migration ?:
            $this->migration = new Migration(clone $this->getFiles(), $this->console);
    }

    /**
     * Start installation process.
     *
     * @throws ErrorException
     *
     * @return bool
     */
    public function install()
    {
        $this->build()->getMigration()->run();

        return $this->notifyUser();
    }

    /**
     * Get the package files.
     *
     * @return \Symfony\Component\Finder\Finder
     */
    public function getFiles()
    {
        return $this->files ?: $this->files = Finder::create()->in($this->directory);
    }

    /**
     * Check manager file exists in package or not.
     *
     * @return bool
     */
    public function hasManagerFile()
    {
        if (app('filesystem')->exists($this->directory.'manager.yml')) {
            return true;
        }

        $this->console->warn("No manager.yml file found in {$this->name} package.");

        return false;
    }

    /**
     * Load the package configuration form file.
     *
     * @return bool
     */
    public function loadManagerFile()
    {
        // @todo If manager.yml file is given then we don't need to search whole project

        return false;
    }

    /**
     * Check to see if we have a valid command class to work.
     *
     * @param  $class
     *
     * @throws Exception
     *
     * @return bool
     */
    public function isValidConsole($class)
    {
        if ($class instanceof Command) {
            return true;
        }

        throw new Exception(get_class($class).' not found.');
    }

    /**
     * Set the name of the package.
     *
     * @param  $name
     *
     * @return $this
     */
    private function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set the directory of the package.
     *
     * @param $path
     *
     * @return $this
     */
    private function setDirectory($path)
    {
        $this->directory = $path;

        return $this;
    }

    /**
     * Set the given console class.
     *
     * @param $class
     *
     * @return $this
     */
    private function setConsole($class)
    {
        if ($this->isValidConsole($class)) {
            $this->console = $class;
        }

        return $this;
    }

    /**
     * Notify user.
     *
     * @return bool
     */
    protected function notifyUser()
    {
        $this->console->line('');

        $this->console->line("{$this->isDone($this->getProviders()->isRegistered())} ".
            "{$this->getProviders()->count()} service provider registered.");

        $this->console->line("{$this->isDone($this->getFacades()->isRegistered())} ".
            "{$this->getFacades()->count()} facade registered.");

        $this->console->line("{$this->isDone($this->getMigration()->isRegistered())} ".
            "{$this->getMigration()->count()} migration file ran.");

        $this->console->line("{$this->isDone($this->getResources()->isRegistered())} ".
            "{$this->getResources()->count()} \"".$this->console->tokenizePackageInfo()['name'].
            "\" resource file publish.");

        return true;
    }

    /**
     * Get resource instance.
     *
     * @return \Qafeen\Manager\Manage\Resource
     */
    public function getResources()
    {
        return $this->resources ?:
            $this->resources = Resource::instance(clone $this->getFiles(), $this->console);
    }

    /**
     * Get the correct symbol on bases of done and not done.
     *
     * @param $ans
     *
     * @return string
     */
    protected function isDone($ans)
    {
        return $ans ? " <fg=green;bold>✓</>": " <fg=red;bold>✗</>";
    }
}
