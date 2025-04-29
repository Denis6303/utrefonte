<?php

namespace App\Repository;

use App\Entity\ListeDiffusion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ListeDiffusion>
 *
 * @method ListeDiffusion|null find($id, $lockMode = null, $lockVersion = null)
 * @method ListeDiffusion|null findOneBy(array $criteria, array $orderBy = null)
 * @method ListeDiffusion[]    findAll()
 * @method ListeDiffusion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListeDiffusionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ListeDiffusion::class);
    }

    public function save(ListeDiffusion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ListeDiffusion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findActiveLists(): array
    {
        return $this->findBy(['actif' => true]);
    }

    public function findInactiveLists(): array
    {
        return $this->findBy(['actif' => false]);
    }

    public function findListsByType(string $type): array
    {
        return $this->findBy(['type' => $type]);
    }

    public function findListsWithEmails(): array
    {
        return $this->createQueryBuilder('ld')
            ->andWhere('ld.lesMails IS NOT NULL')
            ->getQuery()
            ->getResult();
    }

    public function findListsByUser(int $userId): array
    {
        return $this->createQueryBuilder('ld')
            ->andWhere('ld.createur = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }
} 