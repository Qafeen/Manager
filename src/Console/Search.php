<?php
namespace Qafeen\Manager\Console;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Qafeen\Manager\Packages;

/**
 * Search the given package
 *
 * @author Mohammed Mudasir <hello@mudasir.me>
 */
class Search extends Command
{
    /**
     * Search package signature.
     *
     * @var string
     */
    protected $signature   = 'manager:search';

    /**
     * Description
     *
     * @var string
     */
    protected $description = 'Search given package on packagist.org';

    /**
     * Run package search.
     */
    public function handle()
    {

    }
}

