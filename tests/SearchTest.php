<?php

use Mockery as m;
use Qafeen\Manager\Manage\Facade;
use Qafeen\Manager\Manage\Migration;
use Qafeen\Manager\Manage\ServiceProvider;
use Qafeen\Manager\Packages;
use Symfony\Component\Finder\Finder;

class SearchTest extends PHPUnit_Framework_TestCase
{
    public function test_packages_search()
    {
        $result = Packages::instance('laravel/laravel')->search()->first()['name'];

        $this->assertEquals('laravel/laravel', $result);
    }

    public function test_facades_search()
    {
        $console = $this->mockAddConsoleCommand();

        $finder = Finder::create()->in(__DIR__.DIRECTORY_SEPARATOR.'dummy');

        $result = Facade::instance($finder, $console)->search();

        $this->assertEquals([], $result);
    }

    public function test_provider_search()
    {
        $console = $this->mockAddConsoleCommand();

        $finder = Finder::create()->in(__DIR__.DIRECTORY_SEPARATOR.'dummy');

        $result = ServiceProvider::instance($finder, $console)->search();

        $this->assertEquals([], $result);
    }

    public function test_migration_search()
    {
        $console = $this->mockAddConsoleCommand();

        $finder = Finder::create()->in(__DIR__.DIRECTORY_SEPARATOR.'dummy');

        $result = Migration::instance($finder, $console)->run();

        $this->assertEquals(false, $result);
    }

    protected function mockAddConsoleCommand()
    {
        return $this->getMockBuilder('\Qafeen\Manager\Console\Add')
                ->setMethods(['info', 'error', 'line', 'confirm', 'warn'])
                ->getMock();
    }

    public function tearDown()
    {
        m::close();
    }
}
