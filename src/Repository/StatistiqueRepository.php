<?php

namespace App\Repository;

use App\Entity\Statistique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Statistique>
 *
 * @method Statistique|null find($id, $lockMode = null, $lockVersion = null)
 * @method Statistique|null findOneBy(array $criteria, array $orderBy = null)
 * @method Statistique[]    findAll()
 * @method Statistique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatistiqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Statistique::class);
    }

    public function save(Statistique $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Statistique $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
} 