<?php
namespace Qafeen\Manager;

use GuzzleHttp\Client;
use Qafeen\Manager\Traits\Helper;

/**
 * Collection of packages
 *
 * @package Qafeen\Manager
 * @author Mohammed Mudasir <hello@mudasir.me>
 */
class Packages
{
    use Helper;

    /**
     * Packagist URL.
     */
    const PACKAGIST_URL = 'https://packagist.org/';

    /**
     * Guzzle HTTP client
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Raw collection of packages which is downloaded from packagist website.
     *
     * @var \Illuminate\Support\Collection;
     */
    protected $rawPackages;

    /**
     * Name of a package which will be search when get method is called.
     *
     * @var null|string
     */
    protected $packageName;

    /**
     * Packages constructor.
     *
     * @param  string $packageName
     */
    public function __construct($packageName = null)
    {
        $this->client      = new Client;

        $this->packageName = $packageName;
    }

    /**
     * Search the given package.
     *
     * @return mixed
     */
    public function search()
    {
        $url = self::PACKAGIST_URL . 'search.json?q=' . $this->getPackageName();

        $response = $this->client
                         ->get($url)
                         ->getBody()
                         ->getContents();

        $this->rawPackages = collect(json_decode($response, true));

        return collect($this->rawPackages->get('results'));
    }

    /**
     * Get the count of the package.
     *
     * @return integer
     */
    public function count()
    {
        return (int) $this->rawPackages->get('total');
    }

    /**
     * Get the package name
     *
     * @return string
     */
    public function getPackageName()
    {
        return $this->packageName ?: '';
    }
}

