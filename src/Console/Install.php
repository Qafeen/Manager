<?php
namespace Qafeen\Manager\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Application;
use Qafeen\Manager\Manager;
use Qafeen\Manager\Packages;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Install Package
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
     * Start installation process.
     */
    public function handle()
    {
        $packages = $this->getPackages();

        if (! $packages->count()) {
            $this->error('No package found. Make sure you spell it correct as specified on github or packagist.');
        }

        if ($packages->first()['name'] !== $this->getPackageName()) {
            $this->info("\tNo package found by this name \"{$this->getPackageName()}\"");

            return $this->call(
                'manager:install',
                [
                    'packageName' => $this->choice('These are some suggestions', $packages->pluck('name')->toArray())
                ]
            );
        }

        $this->downloadPackage()
             ->runConfiguration();
    }

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
                $this->error($buffer);
            } else {
                $this->info($buffer);
            }
        });

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        echo $process->getOutput();

        return $this;
    }

    public function runConfiguration()
    {
        (new Manager($this->getPackageName(), $this))->install();
    }

    public function getPackages()
    {
        return Packages::search($this->getPackageName());
    }

    public function getPackageName()
    {
        return $this->argument('packageName');
    }
}