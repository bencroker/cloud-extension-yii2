<?php

namespace craft\cloud\runtime\event;

use Bref\Context\Context;
use Bref\Event\Handler;
use Exception;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

class CliHandler implements Handler
{
    public const EXIT_CODE_TIMEOUT = 187;
    protected string $scriptPath = '/var/task/craft';

    public function handle(mixed $event, Context $context): array
    {
        $commandArgs = $event['command'] ?? null;

        if (!$commandArgs) {
            throw new Exception('No command found.');
        }

        $php = PHP_BINARY;
        $command = escapeshellcmd("{$php} {$this->scriptPath} {$commandArgs}");
        $timeout = max(1, $context->getRemainingTimeInMillis() / 1000 - 1);
        $process = Process::fromShellCommandline($command, null, [
            'LAMBDA_INVOCATION_CONTEXT' => json_encode($context, JSON_THROW_ON_ERROR),
        ], null, $timeout);

        try {
            echo "Running command: $command";

            /** @throws ProcessTimedOutException|ProcessFailedException */
            $process->mustRun(function($type, $buffer): void {
                echo $buffer;
            });

            echo "Finished command: $command";
        } catch (ProcessTimedOutException $e) {
            $exitCode = self::EXIT_CODE_TIMEOUT;
            echo "Process timed out for command: $command.\n";
        } catch (\Throwable $e) {
            echo $e->getMessage();
        }

        // 'output' is equivalent to stdout/stderr
        return [
            'exitCode' => $exitCode ?? $process->getExitCode(),
            'output' => $process->getErrorOutput() . $process->getOutput(),
        ];
    }
}
