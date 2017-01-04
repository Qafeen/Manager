<?php
namespace Qafeen\Manager\Console;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Qafeen\Manager\Packages;

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
        $key      = 0;

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

        echo shell_exec("composer require {$packages[$key]['name']}");
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