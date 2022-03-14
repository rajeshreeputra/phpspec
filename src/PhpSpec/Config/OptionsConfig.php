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

namespace PhpSpec\Config;

use Symfony\Component\Console\Output\OutputInterface;

class OptionsConfig
{
    private bool $stopOnFailureEnabled;

    private bool $codeGenerationEnabled;

    private bool $reRunEnabled;

    private bool $fakingEnabled;

    private string|false $bootstrapPath;

    private bool $isVerbose;

    public function __construct(
        bool $stopOnFailureEnabled,
        bool $codeGenerationEnabled,
        bool $reRunEnabled,
        bool $fakingEnabled,
        false|string $bootstrapPath,
        bool $isVerbose
    ) {
        $this->stopOnFailureEnabled  = $stopOnFailureEnabled;
        $this->codeGenerationEnabled = $codeGenerationEnabled;
        $this->reRunEnabled = $reRunEnabled;
        $this->fakingEnabled = $fakingEnabled;
        $this->bootstrapPath = $bootstrapPath;
        $this->isVerbose = $isVerbose;
    }

    
    public function isStopOnFailureEnabled(): bool
    {
        return $this->stopOnFailureEnabled;
    }

    
    public function isCodeGenerationEnabled(): bool
    {
        return $this->codeGenerationEnabled;
    }

    public function isReRunEnabled(): bool
    {
        return $this->reRunEnabled;
    }

    public function isFakingEnabled(): bool
    {
        return $this->fakingEnabled;
    }

    public function getBootstrapPath(): string|false
    {
        return $this->bootstrapPath;
    }

    public function isVerbose(): bool
    {
        return $this->isVerbose;
    }
}
