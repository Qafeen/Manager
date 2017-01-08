<?php

use Mockery as m;

class ConsoleCommandTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testAddConsoleCommand()
    {
        $app = $this->getMockConsole(['addToParent']);
        $command = m::mock('Qafeen\Manager\Console\Add');
        $command->shouldReceive('setLaravel')->once()->with(m::type('Illuminate\Contracts\Foundation\Application'));
        $app->expects($this->once())->method('addToParent')->with($this->equalTo($command))->will($this->returnValue($command));

        $result = $app->add($command);

        $this->assertEquals($command, $result);
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
