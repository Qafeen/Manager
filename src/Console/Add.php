<?php
namespace Qafeen\Manager\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Application;
use Illuminate\Support\Collection;
use Qafeen\Manager\Manage\ConfigFile;
use Qafeen\Manager\Manager;
use Qafeen\Manager\Packages;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Install Package Command
 *
 * @package Qafeen\Manager
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
            return $this->call('add', ['package' => $this->prettify($packages)]);
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

    /**
     * Create composer require command.
     *
     * @return string
     */
    public function composerRequire()
    {
        return "composer require {$this->argument('package')}";
    }

    /**
     * Prettify Package result
     *
     * @param Collection $packages
     * @return mixed
     */
    public function prettify(Collection $packages)
    {
        $summary = [];

        foreach ($packages as $key => $package) {
            $summary[] = [
                'id'   => $key + 1,
                'name' => $this->prettifyPackageInfo($package),
            ];
        }

        return $packages[$this->askPackageKey($summary)]['name'];
    }

    /**
     * Ask user for package key
     *
     * @param  array $summary
     * @return integer
     */
    public function askPackageKey($summary)
    {
        $this->table(['id', 'name'], $summary);

        $selected = $this->ask('Please provide an id');

        $key = collect($summary)->pluck('id')->search($selected);

        if (is_null($key)) {
            $this->warn('Invalid package name or id given.');

            return $this->askPackageKey($summary);
        }

        return $key;
    }

    /**
     * Prettify package details
     *
     * @param  array  $package
     * @return string
     */
    private function prettifyPackageInfo($package)
    {
        $newLine2Tab = PHP_EOL . '       ';

        return "{$package['name']}" .
            " [<fg=green;options=bold>⇩</> " . number_format($package['downloads']) .
            "  <fg=magenta;options=bold>★</> " . number_format($package['favers']) . "] " .
            PHP_EOL . wordwrap($package['description']) .
            $newLine2Tab;
    }
}
