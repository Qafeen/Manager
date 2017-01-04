<?php
namespace Qafeen\Manager\Console;

/**
 * Download Package
 *
 * @author Mohammed Mudasir <hello@mudasir.me>
 */
class Download
{
    /**
     * Manager install console command.
     *
     * @var string
     */
    protected $signature = 'manager:download {packageName : Specify Package name. eg: vendor/package}';

    /**
     * Install provided package.
     *
     * @var string
     */
    protected $description = 'Download the given package.';

    /**
     * Start downloading
     */
    public function handle()
    {

    }
}

