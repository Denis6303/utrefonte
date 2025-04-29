<?php

namespace App\Repository;

use App\Entity\Emplacement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Emplacement>
 *
 * @method Emplacement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Emplacement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Emplacement[]    findAll()
 * @method Emplacement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmplacementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Emplacement::class);
    }

    public function save(Emplacement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Emplacement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findActiveLocations(): array
    {
        return $this->findBy(['actif' => true]);
    }

    public function findInactiveLocations(): array
    {
        return $this->findBy(['actif' => false]);
    }

    public function findLocationsByType(string $type): array
    {
        return $this->findBy(['type' => $type]);
    }

    public function findLocationsByParent(int $parentId): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.parent = :parentId')
            ->setParameter('parentId', $parentId)
            ->getQuery()
            ->getResult();
    }

    public function findRootLocations(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.parent IS NULL')
            ->getQuery()
            ->getResult();
    }
} 