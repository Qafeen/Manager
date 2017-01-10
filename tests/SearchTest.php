<?php

use Mockery as m;
use Qafeen\Manager\Manage\Facade;
use Qafeen\Manager\Manage\Migration;
use Qafeen\Manager\Manage\Resource;
use Qafeen\Manager\Manage\ServiceProvider;
use Qafeen\Manager\Packages;
use Symfony\Component\Finder\Finder;

class SearchTest extends PHPUnit_Framework_TestCase
{
    protected $console;

    /**
     * @var \Symfony\Component\Finder\Finder
     */
    protected $finder;

    public function setUp()
    {
        parent::setUp();

        $this->console = $this->getMockConsole([
            'info', 'error', 'line', 'confirm', 'warn', 'call', 'ask', 'tokenizePackageInfo'
        ]);

        $this->console->method('confirm')->willReturn(true);
        $this->console->method('call')->willReturn(true);
        $this->console->method('ask')->willReturn('');
        $this->console->method('tokenizePackageInfo')->willReturn([
            'name' => 'dummy',
            'version' => '0.1'
        ]);

        $this->finder = Finder::create()->in("tests/dummy");
    }

    public function test_packages_search()
    {
        $result = Packages::instance('laravel/laravel')->search()->first()['name'];

        $this->assertEquals('laravel/laravel', $result);
    }

    public function test_facades_search()
    {
        $result = Facade::instance(clone $this->finder, $this->console)->search();

        $this->assertEquals(['DummyFacade'], $result);
    }

    public function test_provider_search()
    {
        $result = ServiceProvider::instance(clone $this->finder, $this->console)->search();

        $this->assertEquals(['DummyServiceProvider'], $result);
    }

    public function test_migration_search()
    {
        $result = Migration::instance(clone $this->finder, $this->console)->run();

        $this->assertEquals(true, $result);
    }

    public function test_publish_resource_files()
    {
        $resource = Resource::instance(clone $this->finder, $this->console);

        $this->assertEquals(true, $resource->publish());

        $this->assertEquals(2, $resource->count());
    }

    public function tearDown()
    {
        m::close();
    }

    protected function getMockConsole(array $methods)
    {
        $app = m::mock('Illuminate\Contracts\Foundation\Application', ['version' => '5.3']);
        $events = m::mock('Illuminate\Contracts\Events\Dispatcher', ['fire' => null]);

        $console = $this->getMockBuilder('Illuminate\Console\Application')->setMethods($methods)->setConstructorArgs([
            $app, $events, 'test-version',
        ])->getMock();

        return $console;
    }
}
