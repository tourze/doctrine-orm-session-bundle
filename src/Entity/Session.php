<?php

declare(strict_types=1);

namespace Tourze\DoctrineORMSessionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineORMSessionBundle\Repository\SessionRepository;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
#[ORM\Table(name: 'doctrine_sessions')]
#[ORM\Index(name: 'idx_user_id', columns: ['user_id'])]
#[ORM\Index(name: 'idx_expires_at', columns: ['expires_at'])]
#[ORM\Index(name: 'idx_last_activity_at', columns: ['last_activity_at'])]
class Session
{
    public const DEFAULT_LIFETIME = 1440; // 24 minutes

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 128)]
    private string $sessionId;

    #[ORM\Column(type: 'json')]
    private array $sessionData = [];

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $userId = null;

    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    private ?string $ipAddress = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $userAgent = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $lastActivityAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $expiresAt;

    #[ORM\Column(type: 'integer')]
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
}
