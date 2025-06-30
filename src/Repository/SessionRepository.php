<?php

declare(strict_types=1);

namespace Tourze\DoctrineORMSessionBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\DoctrineORMSessionBundle\Entity\Session;

/**
 * @extends ServiceEntityRepository<Session>
 *
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }
    
    public function save(Session $session, bool $flush = false): void
    {
        $this->getEntityManager()->persist($session);
        
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    public function remove(Session $session, bool $flush = false): void
    {
        $this->getEntityManager()->remove($session);
        
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    /**
     * 查找活跃的 session
     */
    public function findActiveSession(string $sessionId): ?Session
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.sessionId = :sessionId')
            ->andWhere('s.expiresAt > :now')
            ->setParameter('sessionId', $sessionId)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult();
    }
    
    /**
     * 清理过期的 sessions
     */
    public function removeExpiredSessions(): int
    {
        return $this->createQueryBuilder('s')
            ->delete()
            ->where('s.expiresAt < :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->execute();
    }
    
    /**
     * 查找用户的所有活跃 sessions
     */
    public function findActiveSessionsByUserId(int $userId): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.userId = :userId')
            ->andWhere('s.expiresAt > :now')
            ->setParameter('userId', $userId)
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('s.lastActivityAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
    
    /**
     * 移除用户的所有 sessions
     */
    public function removeUserSessions(int $userId): int
    {
        return $this->createQueryBuilder('s')
            ->delete()
            ->where('s.userId = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->execute();
    }
}
