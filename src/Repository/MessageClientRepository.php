<?php

namespace App\Repository;

use App\Entity\MessageClient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MessageClient>
 *
 * @method MessageClient|null find($id, $lockMode = null, $lockVersion = null)
 * @method MessageClient|null findOneBy(array $criteria, array $orderBy = null)
 * @method MessageClient[]    findAll()
 * @method MessageClient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessageClient::class);
    }

    public function save(MessageClient $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MessageClient $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findUnreadClientMessages(int $clientId): array
    {
        return $this->createQueryBuilder('mc')
            ->andWhere('mc.client = :clientId')
            ->andWhere('mc.lu = false')
            ->setParameter('clientId', $clientId)
            ->getQuery()
            ->getResult();
    }

    public function findClientMessages(int $clientId): array
    {
        return $this->createQueryBuilder('mc')
            ->andWhere('mc.client = :clientId')
            ->setParameter('clientId', $clientId)
            ->getQuery()
            ->getResult();
    }

    public function findMessagesByStatus(string $status): array
    {
        return $this->findBy(['statut' => $status]);
    }

    public function findImportantClientMessages(int $clientId): array
    {
        return $this->createQueryBuilder('mc')
            ->andWhere('mc.client = :clientId')
            ->andWhere('mc.important = true')
            ->setParameter('clientId', $clientId)
            ->getQuery()
            ->getResult();
    }

    public function findMessagesByType(string $type): array
    {
        return $this->findBy(['type' => $type]);
    }
} 