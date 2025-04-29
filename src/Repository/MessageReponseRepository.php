<?php

namespace App\Repository;

use App\Entity\MessageReponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MessageReponse>
 *
 * @method MessageReponse|null find($id, $lockMode = null, $lockVersion = null)
 * @method MessageReponse|null findOneBy(array $criteria, array $orderBy = null)
 * @method MessageReponse[]    findAll()
 * @method MessageReponse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageReponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessageReponse::class);
    }

    public function save(MessageReponse $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MessageReponse $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findUnreadResponses(int $userId): array
    {
        return $this->createQueryBuilder('mr')
            ->andWhere('mr.destinataire = :userId')
            ->andWhere('mr.messageLu = false')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function findResponsesByMessage(int $messageId): array
    {
        return $this->createQueryBuilder('mr')
            ->andWhere('mr.message = :messageId')
            ->setParameter('messageId', $messageId)
            ->getQuery()
            ->getResult();
    }

    public function findResponsesByUser(int $userId): array
    {
        return $this->createQueryBuilder('mr')
            ->andWhere('mr.expediteur = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function findResponsesByType(string $type): array
    {
        return $this->findBy(['type' => $type]);
    }
} 