<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function save(Message $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Message $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findUnreadMessages(int $userId): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.destinataire = :userId')
            ->andWhere('m.lu = false')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function findSentMessages(int $userId): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.expediteur = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function findReceivedMessages(int $userId): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.destinataire = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function findMessagesByType(string $type): array
    {
        return $this->findBy(['type' => $type]);
    }

    public function findImportantMessages(int $userId): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.destinataire = :userId')
            ->andWhere('m.important = true')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }
} 