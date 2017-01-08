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

    /**
     * @var array
     */
    protected $providers;

    /**
     * Facades (aliases).
     *
     * @var array
     */
    protected $aliases;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * ConfigFile constructor.
     *
     * @param array $providers
     * @param array $aliases
     */
    public function __construct(array $providers = [], array $aliases = [])
    {
        $this->providers = $providers;

        $this->aliases = $aliases;

        $this->filesystem = new Filesystem();
    }

    /**
     * Make config/manager.php file.
     *
     * @return bool
     */
    public function make()
    {
        $content = $this->getManagerStubContent();

        $content = str_replace('@providers', $this->stringify('providers'), $content);

        $content = str_replace('@aliases', $this->stringify('aliases'), $content);

        $this->filesystem->put($this->getManagerFilePath(), $content);

        return true;
    }

    /**
     * Get the configuration details form config/manager.php file.
     *
     * @param  $name
     *
     * @return array|mixed
     */
    public function getFromConfig($name)
    {
        if (!$config = config("manager.$name")) {
            return [];
        }

        return $config;
    }

    /**
     * Get the content from manager stub file.
     *
     * @return string
     */
    protected function getManagerStubContent()
    {
        return file_get_contents($this->getManagerStubFilePath());
    }

    /**
     * Get manager file path.
     *
     * @return string
     */
    protected function getManagerFilePath()
    {
        return config_path('manager.php');
    }

    /**
     * Get manager stub file path.
     *
     * @return string
     */
    protected function getManagerStubFilePath()
    {
        return realpath(__DIR__.'/../stubs/manager.stub');
    }

    /**
     * Convert providers or facade details to template.
     *
     * @param  $name
     *
     * @return string
     */
    protected function stringify($name)
    {
        $classes = array_unique(array_merge(
            $this->suffixClass($this->getFromConfig($name)),
            $this->suffixClass($this->$name)
        ));

        $newLine2Tabs = $this->makeTabs(2, true);

        if ($name == 'providers') {
            return "'$name' => [$newLine2Tabs".
                        implode(','.$newLine2Tabs, $classes).
                    $this->makeTabs(1, true).'],'.PHP_EOL;
        }

        $template = "'$name' => [$newLine2Tabs";

        foreach ($classes as $fullClassName) {
            $template .= "'{$this->getClassName($fullClassName)}' => $fullClassName,$newLine2Tabs";
        }

        $template .= $this->makeTabs(1, true).'],'.PHP_EOL;

        return $template;
    }

    /**
     * Make tabs and add line break dynamically.
     *
     * @param int  $multiply
     * @param bool $newline
     *
     * @return string
     */
    protected function makeTabs($multiply = 1, $newline = false)
    {
        $tab = (!$newline) ? '    ' : PHP_EOL.'    ';

        if ($multiply == 0) {
            return '';
        }

        return $tab.$this->makeTabs($multiply - 1);
    }

    /**
     * Suffix '::class' to class name.
     *
     * @param array $class
     *
     * @return array
     */
    protected function suffixClass(array $class)
    {
        return array_map(function ($class) {
            return "$class::class";
        }, $class);
    }

    /**
     * Get the class name by given full qualified class name.
     *
     * @param string $class
     *
     * @return mixed
     */
    public function getClassName($class)
    {
        return preg_replace('/::class|Facade/i', '', last(explode('\\', $class)));
    }
}
