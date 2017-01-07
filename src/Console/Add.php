<?php
namespace Qafeen\Manager\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Application;
use Illuminate\Support\Collection;
use Qafeen\Manager\Manage\ConfigFile;
use Qafeen\Manager\Manager;
use Qafeen\Manager\Packages;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Install Package Command
 *
 * @author Mohammed Mudasir <hello@mudasir.me>
 */
class Add extends Command
{
    /**
     * Manager install console command.
     *
     * @var string
     */
    protected $signature = 'add {package : Specify Package name. eg: vendor/package:version}';

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
        $packageInfo = $this->tokenizePackageInfo();
        $packages    = $this->getPackages();

        if (! $packages->count()) {
            $this->warn(' No package found. Make sure you spell it correct as specified on github or packagist.');
        }

        if ($packages->first()['name'] !== $packageInfo['name']) {
            $this->warn(" No package found by this name \"{$packageInfo['name']}\"");

            return $this->call(
                'add',
                [
                    'package' => $this->prettifyResults($packages)
                ]
            );
        }

        $this->downloadPackage()
             ->runConfiguration();
    }

    /**
     * Start downloading package.
     *
     * @return \Qafeen\Manager\Console\Add
     */
    public function downloadPackage()
    {
        $process = new Process(
            $this->composerRequire(),
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
        Manager::instance($this->tokenizePackageInfo()['name'], $this)->install();
    }

    /**
     * Search packages from packagist.org
     *
     * @return mixed
     */
    public function getPackages()
    {
        return Packages::instance($this->tokenizePackageInfo()['name'])->search();
    }

    /**
     * Get the package name and version provided by user.
     *
     * @return array
     */
    public function tokenizePackageInfo()
    {
        $info = explode(':', $this->argument('package'));

        return [
            'name'    => $info[0],
            'version' => (count($info) > 1) ? last($info) : null,
        ];
    }

    public function composerRequire()
    {
        return "composer require {$this->argument('package')}";
    }

    public function prettifyResults(Collection $packages)
    {
        $summary = [];

        $newLine2Tab = PHP_EOL . '       ';

        foreach ($packages as $key => $package) {
            // Incrementing key by one and prettifying package result
            $summary[$key + 1] = "{$package['name']}" .
                " [<fg=green;options=bold>⇩</> {$package['downloads']}" .
                "  <fg=magenta;options=bold>★</> {$package['favers']}] " .
                $newLine2Tab . wordwrap($package['description'], 75, $newLine2Tab) .
            $newLine2Tab;
        }

        // Note: We are incrementing key above so we need to minus it by one
        $key = $this->askPackageKey($summary) - 1;

        return $packages[$key]['name'];
    }

    public function askPackageKey($summary)
    {
        if (! $key = collect($summary)->search($this->choice('These are some suggestions', $summary))) {
            $this->warn('Invalid package name provided. Please provide the correct package number');

            return $this->askPackageKey($summary);
        }

        return $key;
    }
}
