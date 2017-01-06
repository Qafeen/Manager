<?php
namespace Qafeen\Manager\Manage;

use Illuminate\Filesystem\Filesystem;
use Qafeen\Manager\Traits\Helper;

/**
 * Handle configuration file.
 *
 * @author Mohammed Mudasir <hello@mudasir.me>
 */
class ConfigFile
{
    use Helper;

    protected $providers;

    protected $aliases;

    protected $filesystem;

    public function __construct(array $providers = [], array $aliases = [])
    {
        $this->providers  = $providers;

        $this->aliases    = $aliases;

        $this->filesystem = new Filesystem;
    }

    public function generate()
    {
        $content  = $this->getManagerStubContent();

        $content  = str_replace("@providers", $this->stringify('providers'), $content);

        $content  = str_replace("@aliases", $this->stringify('aliases'), $content);

        $this->filesystem->put($this->getManagerFile(), $content);

        return true;
    }

    public function getFromConfig($name)
    {
        if (! $config = config("manager.$name")) {
            return [];
        }

        return $config;
    }

    protected function getManagerStubContent()
    {
        return file_get_contents($this->getManagerStubFile());
    }

    protected function getManagerFile()
    {
        return config_path('manager.php');
    }

    protected function getManagerStubFile()
    {
        return realpath(__DIR__ . '/../stubs/manager.stub');
    }

    protected function suffixClass(array $class)
    {
        return array_map(function($class) {
            return "$class::class";
        }, $class);
    }

    protected function stringify($name)
    {
        $stringify = array_unique(array_merge(
            $this->suffixClass($this->getFromConfig($name)),
            $this->suffixClass($this->$name)
        ));

        $newLine2Tabs = $this->makeTabs(2, true);

        return "'$name' => [$newLine2Tabs".
                    implode(','.$newLine2Tabs, $stringify).
            $this->makeTabs(1, true)."],".PHP_EOL;
    }

    protected function makeTabs($multiply = 1, $newline = false)
    {
        $tab = (! $newline) ? '    ' : PHP_EOL.'    ';

        if ($multiply == 0) {
            return '';
        }

        return $tab . $this->makeTabs($multiply - 1);
    }
}
