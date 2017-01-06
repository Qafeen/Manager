<?php
namespace Qafeen\Manager\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Application;
use Qafeen\Manager\Manager;
use Qafeen\Manager\Packages;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Install Package Command
 *
 * @author Mohammed Mudasir <hello@mudasir.me>
 */
class Install extends Command
{
    /**
     * Manager install console command.
     *
     * @var string
     */
    protected $signature = 'manager:install {packageName : Specify Package name. eg: vendor/package}';

    /**
     * Install provided package.
     *
     * @var string
     */
    protected $description = 'Install provided package.';

    /**
     * Entry point for installation
     *
     * @return mixed
     */
    public function handle()
    {
        $packages = $this->getPackages();

        if (! $packages->count()) {
            $this->warn(' No package found. Make sure you spell it correct as specified on github or packagist.');
        }

        if ($packages->first()['name'] !== $this->getPackageName()) {
            $this->warn(" No package found by this name \"{$this->getPackageName()}\"");

            return $this->call(
                'manager:install',
                [
                    'packageName' => $this->choice(
                        'These are some suggestions',
                        $packages->pluck('name')->toArray()
                    )
                ]
            );
        }

        $this
             ->downloadPackage()
             ->runConfiguration();
    }

    /**
     * Start downloading package.
     *
     * @return \Qafeen\Manager\Console\Install
     */
    public function downloadPackage()
    {
        $process = new Process(
            "composer require {$this->getPackageName()}",
            null,
            null,
            null,
            ini_get('max_execution_time')
        );

        $process->run(function ($type, $buffer) {
            if (Process::ERR === $type) {
                $this->warn(trim($buffer));
            } else {
                $this->info(trim($buffer));
            }
        });

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $this;
    }

    /**
     * Start installation process
     *
     * @return void
     */
    public function runConfiguration()
    {
        Manager::instance($this->getPackageName(), $this)->install();
    }

    /**
     * Search packages from packagist.org
     *
     * @return mixed
     */
    public function getPackages()
    {
        return Packages::instance($this->getPackageName())->search();
    }

    /**
     * Get the package name provided by user.
     *
     * @return string
     */
    public function getPackageName()
    {
        $name = $this->argument('packageName');

        return is_array($name) ? $name[0] : $name;
    }
}
