<?php

declare(strict_types=1);

namespace Tourze\DoctrineORMSessionBundle\Tests\Handler;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Tourze\DoctrineORMSessionBundle\Entity\Session;
use Tourze\DoctrineORMSessionBundle\Handler\DoctrineSessionHandler;
use Tourze\DoctrineORMSessionBundle\Repository\SessionRepository;

class DoctrineSessionHandlerTest extends TestCase
{
    private SessionRepository|MockObject $repository;
    private RequestStack|MockObject $requestStack;
    private DoctrineSessionHandler $handler;
    
    public function testOpen(): void
    {
        $result = $this->handler->open('/tmp', 'PHPSESSID');
        $this->assertTrue($result);
    }
    
    public function testClose(): void
    {
        $result = $this->handler->close();
        $this->assertTrue($result);
    }
    
    public function testReadWithExistingSession(): void
    {
        $sessionId = 'test_session_id';
        $sessionData = ['user_id' => 123, 'role' => 'admin'];

        $session = new Session(
            sessionId: $sessionId,
            sessionData: $sessionData
        );

        $this->repository->expects($this->once())
            ->method('findActiveSession')
            ->with($sessionId)
            ->willReturn($session);

        $result = $this->handler->read($sessionId);

        $this->assertSame(serialize($sessionData), $result);
    }
    
    public function testReadWithNonExistingSession(): void
    {
        $sessionId = 'non_existing_session';

        $this->repository->expects($this->once())
            ->method('findActiveSession')
            ->with($sessionId)
            ->willReturn(null);

        $result = $this->handler->read($sessionId);

        $this->assertSame('', $result);
    }
    
    public function testWriteNewSession(): void
    {
        $sessionId = 'new_session_id';
        $data = serialize(['user_id' => 456]);

        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('getClientIp')
            ->willReturn('192.168.1.100');

        $request->headers = $this->createMock(\Symfony\Component\HttpFoundation\HeaderBag::class);
        $request->headers->expects($this->once())
            ->method('get')
            ->with('User-Agent')
            ->willReturn('Test Browser');

        $this->requestStack->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($request);

        $this->repository->expects($this->once())
            ->method('findActiveSession')
            ->with($sessionId)
            ->willReturn(null);

        $this->repository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Session::class), true);

        $result = $this->handler->write($sessionId, $data);

        $this->assertTrue($result);
    }
    
    public function testWriteUpdateExistingSession(): void
    {
        $sessionId = 'existing_session_id';
        $oldData = ['user_id' => 123];
        $newData = ['user_id' => 123, 'role' => 'admin'];

        $session = new Session(
            sessionId: $sessionId,
            sessionData: $oldData
        );

        $this->repository->expects($this->once())
            ->method('findActiveSession')
            ->with($sessionId)
            ->willReturn($session);

        $this->repository->expects($this->once())
            ->method('save')
            ->with($session, true);

        $result = $this->handler->write($sessionId, serialize($newData));

        $this->assertTrue($result);
        $this->assertSame($newData, $session->getSessionData());
    }
    
    public function testDestroy(): void
    {
        $sessionId = 'session_to_destroy';

        $session = new Session(
            sessionId: $sessionId,
            sessionData: []
        );

        $this->repository->expects($this->once())
            ->method('findActiveSession')
            ->with($sessionId)
            ->willReturn($session);

        $this->repository->expects($this->once())
            ->method('remove')
            ->with($session, true);

        $result = $this->handler->destroy($sessionId);

        $this->assertTrue($result);
    }
    
    public function testDestroyNonExistingSession(): void
    {
        $sessionId = 'non_existing_session';

        $this->repository->expects($this->once())
            ->method('findActiveSession')
            ->with($sessionId)
            ->willReturn(null);

        $this->repository->expects($this->never())
            ->method('remove');

        $result = $this->handler->destroy($sessionId);

        $this->assertTrue($result);
    }
    
    public function testGc(): void
    {
        $lifetime = 3600;

        $this->repository->expects($this->once())
            ->method('removeExpiredSessions')
            ->willReturn(5);

        $result = $this->handler->gc($lifetime);

        $this->assertEquals(5, $result);
    }
    
    public function testWriteWithUserId(): void
    {
        $sessionId = 'session_with_user';
        $userId = 789;
        $data = serialize(['user_id' => $userId]);

        $this->repository->expects($this->once())
            ->method('findActiveSession')
            ->with($sessionId)
            ->willReturn(null);

        $savedSession = null;
        $this->repository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Session $session) use (&$savedSession) {
                $savedSession = $session;
                return true;
            }), true);

        $this->handler->write($sessionId, $data);

        $this->assertNotNull($savedSession);
        $this->assertEquals($userId, $savedSession->getUserId());
    }
    
    protected function setUp(): void
    {
        $this->repository = $this->createMock(SessionRepository::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->handler = new DoctrineSessionHandler(
            $this->repository,
            $this->requestStack
        );
    }
}