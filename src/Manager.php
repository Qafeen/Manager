<?php
namespace Qafeen\Manager;

use Exception;
use Illuminate\Console\Command;
use Qafeen\Manager\Manage\ConfigFile;
use Qafeen\Manager\Manage\Facade;
use Qafeen\Manager\Traits\Helper;
use Symfony\Component\Finder\Finder;
use Qafeen\Manager\Manage\ServiceProvider;

/**
 * Package manager will handling installing, uninstalling or deleting packages.
 *
 * @package Qafeen\Manager
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
     * @var
     */
    protected $directory;

    /**
     * List of service provider which package contain.
     *
     * @var
     */
    protected $serviceProviders;

    /**
     * Console class to notify user.
     *
     * @var \Illuminate\Console\Command
     */
    protected $console;

    /**
     * @param  string   $name
     * @param  mixed    $console
     */
    public function __construct($name = null, $console)
    {
        $this->setName($name)
             ->setDirectory("vendor/$name/")
             ->setConsole($console);
    }

    /**
     * Start installation process
     *
     * @return bool
     */
    public function install()
    {
        if ($this->hasManagerFile()) {
            return $this->loadManagerFile();
        }

        $providers = ServiceProvider::instance($this->getFiles(), $this->console)->search();

        $facades   = Facade::instance($this->getFiles(), $this->console)->search();

        return ConfigFile::instance($providers, $facades)->make();
    }

    /**
     * Get the package files.
     *
     * @return Finder|\Symfony\Component\Finder\SplFileInfo[]
     */
    public function getFiles()
    {
        return Finder::create()->in($this->directory);
    }

    /**
     * Check manager file exists in package or not.
     *
     * @return bool
     */
    public function hasManagerFile()
    {
        if (app('filesystem')->exists($this->directory . "manager.yml")) {
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
     * Check to see if we have a valid command class to work
     *
     * @param  $class
     * @return bool
     * @throws Exception
     */
    public function isValidConsole($class)
    {
        if ($class instanceof Command) {
            return true;
        }

        throw new Exception(get_class($class) . " not found.");
    }

    /**
     * Set the name of the package.
     *
     * @param  $name
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
     * @return $this
     */
    private function setConsole($class)
    {
        if ($this->isValidConsole($class)) {
            $this->console = $class;
        }

        return $this;
    }
}

