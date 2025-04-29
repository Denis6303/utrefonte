<?php

namespace App\Repository;

use App\Entity\StatistiqueClient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StatistiqueClient>
 *
 * @method StatistiqueClient|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatistiqueClient|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatistiqueClient[]    findAll()
 * @method StatistiqueClient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatistiqueClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatistiqueClient::class);
    }

    public function save(StatistiqueClient $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(StatistiqueClient $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
} 