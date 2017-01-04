<?php
namespace Qafeen\Manager;

use GuzzleHttp\Client;

/**
 * Collection of packages
 *
 * @author Mohammed Mudasir <hello@mudasir.me>
 */
class Packages
{
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
     * Search the given package by name
     *
     * @param $packageName
     * @return mixed
     */
    public static function search($packageName)
    {
        return (new static($packageName))->get();
    }

    /**
     * Get the list of packages.
     *
     * @return \Illuminate\Support\Collection
     */
    public function get()
    {
        $url = self::PACKAGIST_URL . 'search.json?q=' . $this->getPackageName();

        $response = $this->client
                         ->get($url)
                         ->getBody()
                         ->getContents();

        $this->rawPackages = collect(json_decode($response, true));

        return collect($this->rawPackages->get('results'));
    }

    public function count()
    {
        return $this->rawPackages->get('total');
    }

    public function getPackageName()
    {
        return $this->packageName ?: '';
    }
}

