<?php

namespace App\Repository;

use App\Entity\Pays;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pays>
 *
 * @method Pays|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pays|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pays[]    findAll()
 * @method Pays[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaysRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pays::class);
    }

    public function save(Pays $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Pays $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
} 