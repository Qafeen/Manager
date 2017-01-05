<?php
namespace Qafeen\Manager;

use Illuminate\Console\Command;
use Exception;
use Symfony\Component\Finder\Finder;
use hanneskod\classtools\Iterator\ClassIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Package manager will handling installing, uninstalling or deleting packages.
 *
 * @author Mohammed Mudasir <hello@mudasir.me>
 */
class Manager
{
    /**
     * An array which will hold all commands which need to be executed once
     * installer command "manager:install" has completed getting package from
     * packagist.org
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

    protected $directory;

    protected $files;

    protected $serviceProviders;

    /**
     * @var
     */
    protected $console;

    /**
     * Manager constructor.
     *
     * @param  string   $name
     * @param  mixed    $console
     */
    public function __construct($name = null, $console)
    {
        $this->setName($name)
             ->setDirectory("vendor/$name/")
             ->setConsole($console);
    }

    public function install()
    {
        if ($this->hasManagerFile()) {
            return $this->loadManagerFile();
        }

        $this->console->info("No manager.yml file found in {$this->name} package.");
        $this->console->info("Searching {$this->directory} directory for service providers.");

        ManageServiceProvider::instance($this->getFiles(), $this->console)->search();

        if (! $this->console->confirm("Add it to your `config/app.php` file?", true)) {
            return false;
        }
    }

    public function getFiles()
    {
        return $this->files = $this->files ?: Finder::create()->in($this->directory);
    }

    public function hasManagerFile()
    {
        return app('filesystem')->exists($this->directory . "manager.yml");
    }

    public function loadManagerFile()
    {
        // @todo If manager.yml file is given then we don't need to search whole project

        return false;
    }

    public function isValidConsole($class)
    {
        if ($class instanceof Command) {
            return true;
        }

        throw new Exception(get_class($class) . " not found.");
    }

    private function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    private function setDirectory($path)
    {
        $this->directory = $path;

        return $this;
    }

    private function setConsole($class)
    {
        if ($this->isValidConsole($class)) {
            $this->console = $class;
        }

        return $this;
    }
}

