<?php

declare(strict_types=1);

namespace Phlib\ConsoleProcess\Command;

use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class BackgroundCommandTest extends TestCase
{
    use PHPMock;

    /**
     * @var Application
     */
    protected $application;

    /**
     * @var string
     */
    protected $commandName = 'foo:bar';

    /**
     * @var Stub\BackgroundCommandStub|MockObject
     */
    protected $command;

    /**
     * @var CommandTester
     */
    protected $tester;

    protected function setUp(): void
    {
        parent::setUp();

        $this->application = new Application();
        $this->application->add(new Stub\BackgroundCommandStub($this->commandName));

        $this->command = $this->application->find($this->commandName);
        $this->tester = new CommandTester($this->command);
    }

    public function testInstanceOfConsoleCommand(): void
    {
        static::assertInstanceOf(Command::class, $this->command);
    }

    public function testBaseCommandClassIsCalled(): void
    {
        static::assertSame($this->commandName, $this->command->getName());
    }

    public function testDefaultSignalCallbacksAreCreated(): void
    {
        $pcntl_signal = $this->getFunctionMock(__NAMESPACE__, 'pcntl_signal');
        $pcntl_signal->expects(static::exactly(2))
            ->withConsecutive([SIGTERM], [SIGINT]);

        $this->tester->execute([]);
    }
}
