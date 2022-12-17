<?php

/*
 * This file is part of PhpSpec, A php toolset to drive emergent
 * design by specification.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpSpec\Process\ReRunner;

use PhpSpec\Process\Context\ExecutionContext;
use Symfony\Component\Process\PhpExecutableFinder;

final class PcntlReRunner extends PhpExecutableReRunner
{
    private ?ExecutionContext $executionContext = null;

    public static function withExecutionContext(PhpExecutableFinder $phpExecutableFinder, ExecutionContext $executionContext): static
    {
        $reRunner = new static($phpExecutableFinder);
        $reRunner->executionContext = $executionContext;

        return $reRunner;
    }

    
    public function isSupported(): bool
    {
        return (php_sapi_name() == 'cli')
            && $this->getExecutablePath()
            && function_exists('pcntl_exec');
    }

    /**
     * Kills the current process and starts a new one
     */
    public function reRunSuite(): void
    {
        /** @var string $executablePath because isSupported was called */
        $executablePath = $this->getExecutablePath();

        $args = $_SERVER['argv'] ?? [];
        $env = $this->executionContext ? $this->executionContext->asEnv() : array();

        $env = array_filter(
            array_merge($env, $_SERVER),
            fn($x): bool => !is_array($x)
        );

        pcntl_exec($executablePath, $args, $env);
    }
}
