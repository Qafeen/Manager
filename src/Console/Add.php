<?php

namespace Qafeen\Manager\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Qafeen\Manager\Manager;
use Qafeen\Manager\Packages;
use Qafeen\Manager\ProcessBuilder;

/**
 * Install Package Command.
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
     * Entry point for installation.
     *
     * @return mixed
     */
    public function handle()
    {
        $packageInfo = $this->tokenizePackageInfo();
        $packages = $this->getPackages();
        $total = $packages->count();

        if (!$total) {
            $this->warn(' No package found. Make sure you spell it correct as specified on github or packagist.');
        }

        if ($packages->first()['name'] !== $packageInfo['name']) {
            $this->warn($total.' package'.($total > 1 ? 's' : '').' found by given name.');

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
        ProcessBuilder::instance($this)->run($this->composerRequire());

        return $this;
    }

    /**
     * Start installation process.
     *
     * @return void
     */
    public function runConfiguration()
    {
        Manager::instance($this->tokenizePackageInfo()['name'], $this)->install();
    }

    /**
     * Search packages from packagist.org.
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
     * Prettify Package result.
     *
     * @param Collection $packages
     *
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
     * Ask user for package key.
     *
     * @param        $summary
     * @param string $message
     *
     * @return mixed
     */
    public function askPackageKey($summary, $message = 'Please provide an id')
    {
        $this->table(['id', 'name'], $summary);

        $selected = $this->ask($message);

        $key = collect($summary)->pluck('id')->search($selected);

        if ($key === false) {
            $this->warn('Invalid package name or id given.');

            return $this->askPackageKey($summary, 'Please provide a valid id');
        }

        return $key;
    }

    /**
     * Prettify package details.
     *
     * @param array $package
     *
     * @return string
     */
    private function prettifyPackageInfo($package)
    {
        $newLine2Tab = PHP_EOL.'       ';
        $downloads = number_format($package['downloads']);
        $stars = number_format($package['favers']);

        $reputation = " [<fg=green;options=bold>⇩ $downloads</>".
                      " <fg=magenta;options=bold>★ $stars</>]";

        $holdSpace = strlen($package['name'].$downloads.$stars);

        $insertSpace = str_repeat(' ', 65 - $holdSpace);

        return "<fg=yellow>{$package['name']}</>".$insertSpace.$reputation.PHP_EOL.
                wordwrap($package['description']).PHP_EOL.
                $package['repository'].
            $newLine2Tab;
    }
}
