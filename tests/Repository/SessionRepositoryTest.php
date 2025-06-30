<?php

declare(strict_types=1);

namespace Tourze\DoctrineORMSessionBundle\Tests\Repository;

use PHPUnit\Framework\TestCase;
use Tourze\DoctrineORMSessionBundle\Repository\SessionRepository;

class SessionRepositoryTest extends TestCase
{
    public function testRepositoryExists(): void
    {
        // 仅测试仓储类存在且可以加载
        $this->assertTrue(class_exists(SessionRepository::class));
        
        // 验证继承关系
        $reflection = new \ReflectionClass(SessionRepository::class);
        $this->assertTrue($reflection->isSubclassOf(\Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository::class));
    }
    
    public function testRepositoryMethods(): void
    {
        $reflection = new \ReflectionClass(SessionRepository::class);
        
        // 验证必要的方法存在
        $this->assertTrue($reflection->hasMethod('save'));
        $this->assertTrue($reflection->hasMethod('remove'));
        $this->assertTrue($reflection->hasMethod('findActiveSession'));
        $this->assertTrue($reflection->hasMethod('removeExpiredSessions'));
        $this->assertTrue($reflection->hasMethod('findActiveSessionsByUserId'));
        $this->assertTrue($reflection->hasMethod('removeUserSessions'));
    }
}