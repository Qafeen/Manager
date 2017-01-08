<?php

namespace Qafeen\Manager;

use Qafeen\Manager\Traits\Helper;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Custom Process builder.
 *
 * @author  Mohammed Mudasir <hello@mudasir.me>
 */
class ProcessBuilder
{
    use Helper;

    /**
     * @var \Illuminate\Console\Command
     */
    protected $console;

    /**
     * ProcessBuilder constructor.
     *
     * @param $console
     */
    public function __construct($console)
    {
        $this->console = $console;
    }

    /**
     * Run the created process builder.
     *
     * @param $command
     *
     * @return bool
     */
    public function run($command)
    {
        $process = new Process(
            $command,
            null,
            null,
            null,
            ini_get('max_execution_time')
        );

        $process->run(function ($type, $buffer) {
            if (Process::ERR === $type) {
                $this->console->warn(trim($buffer));
            } else {
                $this->console->info(trim($buffer));
            }
        });

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return true;
    }
}
