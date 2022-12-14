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

namespace PhpSpec\Wrapper\Subject;

use PhpSpec\Loader\Node\ExampleNode;
use PhpSpec\Matcher\Matcher;
use PhpSpec\Wrapper\Subject\Expectation\ConstructorDecorator;
use PhpSpec\Wrapper\Subject\Expectation\DispatcherDecorator;
use PhpSpec\Wrapper\Subject\Expectation\Expectation;
use PhpSpec\Wrapper\Subject\Expectation\ThrowExpectation;
use PhpSpec\Wrapper\Subject\Expectation\UnwrapDecorator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use PhpSpec\Runner\MatcherManager;
use PhpSpec\Wrapper\Unwrapper;

class ExpectationFactory
{
    private ExampleNode $example;
    private EventDispatcherInterface $dispatcher;
    private MatcherManager $matchers;

    public function __construct(ExampleNode $example, EventDispatcherInterface $dispatcher, MatcherManager $matchers)
    {
        $this->example = $example;
        $this->dispatcher = $dispatcher;
        $this->matchers = $matchers;
    }

    public function create(string $expectation, mixed $subject, array $arguments = array()): Expectation
    {
        if (str_starts_with($expectation, 'shouldNot')) {
            return $this->createNegative(lcfirst(substr($expectation, 9)), $subject, $arguments);
        }

        if (str_starts_with($expectation, 'should')) {
            return $this->createPositive(lcfirst(substr($expectation, 6)), $subject, $arguments);
        }

        throw new \RuntimeException('Could not create match');
    }

    private function createPositive(string $name, mixed $subject, array $arguments = array()): Expectation
    {
        if (strtolower($name) === 'throw') {
            return $this->createDecoratedExpectation("PositiveThrow", $name, $subject, $arguments);
        }

        if (strtolower($name) === 'trigger') {
            return $this->createDecoratedExpectation("PositiveTrigger", $name, $subject, $arguments);
        }

        return $this->createDecoratedExpectation("Positive", $name, $subject, $arguments);
    }

    private function createNegative(string $name, mixed $subject, array $arguments = array()): Expectation
    {
        if (strtolower($name) === 'throw') {
            return $this->createDecoratedExpectation("NegativeThrow", $name, $subject, $arguments);
        }

        if (strtolower($name) === 'trigger') {
            return $this->createDecoratedExpectation("NegativeTrigger", $name, $subject, $arguments);
        }

        return $this->createDecoratedExpectation("Negative", $name, $subject, $arguments);
    }

    private function createDecoratedExpectation(string $expectation, string $name, mixed $subject, array $arguments): Expectation
    {
        $matcher = $this->findMatcher($name, $subject, $arguments);

        /** @var class-string<Expectation> $expectation */
        $expectation = "\\PhpSpec\\Wrapper\\Subject\\Expectation\\".$expectation;

        $expectation = new $expectation($matcher);

        if ($expectation instanceof ThrowExpectation) {
            return $expectation;
        }

        return $this->decoratedExpectation($expectation, $matcher);
    }

    private function findMatcher(string $name, mixed $subject, array $arguments = array()): Matcher
    {
        $unwrapper = new Unwrapper();
        $arguments = $unwrapper->unwrapAll($arguments);

        return $this->matchers->find($name, $subject, $arguments);
    }

    private function decoratedExpectation(Expectation $expectation, Matcher $matcher): ConstructorDecorator
    {
        $dispatcherDecorator = new DispatcherDecorator($expectation, $this->dispatcher, $matcher, $this->example);
        $unwrapperDecorator = new UnwrapDecorator($dispatcherDecorator, new Unwrapper());
        $constructorDecorator = new ConstructorDecorator($unwrapperDecorator);

        return $constructorDecorator;
    }
}
