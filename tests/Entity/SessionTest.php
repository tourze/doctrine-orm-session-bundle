<?php

declare(strict_types=1);

namespace Tourze\DoctrineORMSessionBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Tourze\DoctrineORMSessionBundle\Entity\Session;

class SessionTest extends TestCase
{
    public function testCreateSession(): void
    {
        $sessionId = 'test_session_id';
        $sessionData = ['user_id' => 123, 'role' => 'admin'];
        $userId = 456;
        $ipAddress = '192.168.1.1';
        $userAgent = 'Mozilla/5.0';
        
        $session = new Session(
            sessionId: $sessionId,
            sessionData: $sessionData,
            userId: $userId,
            ipAddress: $ipAddress,
            userAgent: $userAgent
        );
        
        $this->assertSame($sessionId, $session->getSessionId());
        $this->assertSame($sessionData, $session->getSessionData());
        $this->assertSame($userId, $session->getUserId());
        $this->assertSame($ipAddress, $session->getIpAddress());
        $this->assertSame($userAgent, $session->getUserAgent());
        $this->assertInstanceOf(\DateTimeImmutable::class, $session->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $session->getLastActivityAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $session->getExpiresAt());
    }
    
    public function testUpdateLastActivity(): void
    {
        $session = new Session(
            sessionId: 'test_id',
            sessionData: []
        );
        
        $originalLastActivity = $session->getLastActivityAt();
        sleep(1); // 确保时间差异
        
        $session->updateLastActivity();
        
        $this->assertGreaterThan($originalLastActivity, $session->getLastActivityAt());
        $this->assertGreaterThan($originalLastActivity, $session->getExpiresAt());
    }
    
    public function testIsExpired(): void
    {
        $session = new Session(
            sessionId: 'test_id',
            sessionData: [],
            lifetime: 1 // 1秒后过期
        );
        
        $this->assertFalse($session->isExpired());
        
        sleep(2); // 等待过期
        
        $this->assertTrue($session->isExpired());
    }
    
    public function testUpdateSessionData(): void
    {
        $session = new Session(
            sessionId: 'test_id',
            sessionData: ['key1' => 'value1']
        );
        
        $newData = ['key2' => 'value2', 'key3' => 'value3'];
        $session->setSessionData($newData);
        
        $this->assertSame($newData, $session->getSessionData());
    }
    
    public function testDefaultLifetime(): void
    {
        $session = new Session(
            sessionId: 'test_id',
            sessionData: []
        );
        
        // 默认生命周期应该是 1440 秒（24分钟）
        $expectedExpiry = $session->getCreatedAt()->modify('+1440 seconds');
        $this->assertEquals(
            $expectedExpiry->getTimestamp(),
            $session->getExpiresAt()->getTimestamp(),
            '',
            1 // 允许1秒误差
        );
    }
    
    public function testCustomLifetime(): void
    {
        $customLifetime = 3600; // 1小时
        $session = new Session(
            sessionId: 'test_id',
            sessionData: [],
            lifetime: $customLifetime
        );
        
        $expectedExpiry = $session->getCreatedAt()->modify("+{$customLifetime} seconds");
        $this->assertEquals(
            $expectedExpiry->getTimestamp(),
            $session->getExpiresAt()->getTimestamp(),
            '',
            1 // 允许1秒误差
        );
    }
}