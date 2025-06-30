<?php

declare(strict_types=1);

namespace Tourze\DoctrineORMSessionBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\DoctrineORMSessionBundle\Command\SessionGcCommand;
use Tourze\DoctrineORMSessionBundle\Repository\SessionRepository;

class SessionGcCommandTest extends TestCase
{
    public function testExecuteWithExpiredSessions(): void
    {
        $sessionRepository = $this->createMock(SessionRepository::class);
        $sessionRepository->expects($this->once())
            ->method('removeExpiredSessions')
            ->willReturn(5);

        $command = new SessionGcCommand($sessionRepository);
        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $this->assertSame(0, $commandTester->getStatusCode());
        $this->assertStringContainsString('成功清理 5 个过期的 session 记录', $commandTester->getDisplay());
    }

    public function testExecuteWithNoExpiredSessions(): void
    {
        $sessionRepository = $this->createMock(SessionRepository::class);
        $sessionRepository->expects($this->once())
            ->method('removeExpiredSessions')
            ->willReturn(0);

        $command = new SessionGcCommand($sessionRepository);
        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $this->assertSame(0, $commandTester->getStatusCode());
        $this->assertStringContainsString('没有发现过期的 session 记录', $commandTester->getDisplay());
    }

    public function testExecuteWithException(): void
    {
        $sessionRepository = $this->createMock(SessionRepository::class);
        $sessionRepository->expects($this->once())
            ->method('removeExpiredSessions')
            ->willThrowException(new \Exception('Database error'));

        $command = new SessionGcCommand($sessionRepository);
        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $this->assertSame(1, $commandTester->getStatusCode());
        $this->assertStringContainsString('清理失败: Database error', $commandTester->getDisplay());
    }
}