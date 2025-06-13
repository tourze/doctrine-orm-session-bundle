<?php

declare(strict_types=1);

namespace Tourze\DoctrineORMSessionBundle\Handler;

use Symfony\Component\HttpFoundation\RequestStack;
use Tourze\DoctrineORMSessionBundle\Entity\Session;
use Tourze\DoctrineORMSessionBundle\Repository\SessionRepository;

class DoctrineSessionHandler implements \SessionHandlerInterface
{
    public function __construct(
        private SessionRepository $repository,
        private RequestStack $requestStack,
        private int $defaultLifetime = Session::DEFAULT_LIFETIME
    ) {}

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $sessionId): string
    {
        $session = $this->repository->findActiveSession($sessionId);

        if ($session === null) {
            return '';
        }

        // 更新最后活动时间
        $session->updateLastActivity();
        $this->repository->save($session, true);

        return serialize($session->getSessionData());
    }

    public function write(string $sessionId, string $data): bool
    {
        try {
            $sessionData = @unserialize($data);
            if ($sessionData === false && $data !== 'b:0;') {
                $sessionData = [];
            }

            $session = $this->repository->findActiveSession($sessionId);

            if ($session === null) {
                // 创建新 session
                $request = $this->requestStack->getCurrentRequest();
                $ipAddress = null;
                $userAgent = null;

                if ($request !== null) {
                    $ipAddress = $request->getClientIp();
                    $userAgent = $request->headers->get('User-Agent');
                }

                $session = new Session(
                    sessionId: $sessionId,
                    sessionData: $sessionData,
                    userId: $this->extractUserId($sessionData),
                    ipAddress: $ipAddress,
                    userAgent: $userAgent,
                    lifetime: $this->defaultLifetime
                );
            } else {
                // 更新现有 session
                $session->setSessionData($sessionData);
                $session->updateLastActivity();

                // 更新用户 ID（如果有变化）
                $userId = $this->extractUserId($sessionData);
                if ($userId !== null) {
                    $session->setUserId($userId);
                }
            }

            $this->repository->save($session, true);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 从 session 数据中提取用户 ID
     */
    private function extractUserId(array $sessionData): ?int
    {
        // 检查常见的用户 ID 键名
        $userIdKeys = ['user_id', 'userId', '_security_main'];

        foreach ($userIdKeys as $key) {
            if (isset($sessionData[$key])) {
                if (is_int($sessionData[$key])) {
                    return $sessionData[$key];
                }

                // 处理 Symfony Security 的情况
                if ($key === '_security_main' && is_string($sessionData[$key])) {
                    $securityData = @unserialize($sessionData[$key]);
                    if (is_object($securityData) && method_exists($securityData, 'getUserIdentifier')) {
                        $identifier = $securityData->getUserIdentifier();
                        if (is_numeric($identifier)) {
                            return (int) $identifier;
                        }
                    }
                }
            }
        }

        return null;
    }

    public function destroy(string $sessionId): bool
    {
        $session = $this->repository->findActiveSession($sessionId);

        if ($session !== null) {
            $this->repository->remove($session, true);
        }

        return true;
    }

    public function gc(int $maxLifetime): int|false
    {
        try {
            return $this->repository->removeExpiredSessions();
        } catch (\Exception $e) {
            return false;
        }
    }
}
