<?php

declare(strict_types=1);

namespace Tourze\DoctrineORMSessionBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineORMSessionBundle\Repository\SessionRepository;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
#[ORM\Table(name: 'doctrine_sessions', options: ['comment' => '会话存储表'])]
#[ORM\Index(name: 'idx_user_id', columns: ['user_id'])]
#[ORM\Index(name: 'idx_expires_at', columns: ['expires_at'])]
#[ORM\Index(name: 'idx_last_activity_at', columns: ['last_activity_at'])]
class Session implements \Stringable
{
    public const DEFAULT_LIFETIME = 1440; // 24 minutes

    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 128, options: ['comment' => '会话ID'])]
    private string $sessionId;

    #[ORM\Column(type: Types::JSON, options: ['comment' => '会话数据'])]
    private array $sessionData = [];

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '用户ID'])]
    private ?int $userId = null;

    #[ORM\Column(type: Types::STRING, length: 45, nullable: true, options: ['comment' => 'IP地址'])]
    private ?string $ipAddress = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '用户代理'])]
    private ?string $userAgent = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '创建时间'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '最后活动时间'])]
    private \DateTimeImmutable $lastActivityAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '过期时间'])]
    private \DateTimeImmutable $expiresAt;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '会话生命周期（秒）'])]
    private int $lifetime;

    public function __construct(
        string $sessionId,
        array $sessionData,
        ?int $userId = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
        int $lifetime = self::DEFAULT_LIFETIME
    )
    {
        $this->sessionId = $sessionId;
        $this->sessionData = $sessionData;
        $this->userId = $userId;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
        $this->lifetime = $lifetime;

        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->lastActivityAt = $now;
        $this->expiresAt = $now->modify("+{$lifetime} seconds");
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function getSessionData(): array
    {
        return $this->sessionData;
    }

    public function setSessionData(array $sessionData): void
    {
        $this->sessionData = $sessionData;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): void
    {
        $this->userId = $userId;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getLastActivityAt(): \DateTimeImmutable
    {
        return $this->lastActivityAt;
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function updateLastActivity(): void
    {
        $now = new \DateTimeImmutable();
        $this->lastActivityAt = $now;
        $this->expiresAt = $now->modify("+{$this->lifetime} seconds");
    }

    public function isExpired(): bool
    {
        return $this->expiresAt < new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->sessionId;
    }
}
