<?php

namespace App\Repository;

use App\Entity\GAnalytics;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GAnalytics>
 *
 * @method GAnalytics|null find($id, $lockMode = null, $lockVersion = null)
 * @method GAnalytics|null findOneBy(array $criteria, array $orderBy = null)
 * @method GAnalytics[]    findAll()
 * @method GAnalytics[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GAnalyticsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GAnalytics::class);
    }

    public function save(GAnalytics $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(GAnalytics $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findActiveConfigurations(): array
    {
        return $this->findBy(['actif' => true]);
    }

    public function findInactiveConfigurations(): array
    {
        return $this->findBy(['actif' => false]);
    }

    public function findConfigurationsByType(string $type): array
    {
        return $this->findBy(['type' => $type]);
    }

    public function findDefaultConfiguration(): ?GAnalytics
    {
        return $this->findOneBy(['defaut' => true]);
    }
} 