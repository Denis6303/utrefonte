<?php

namespace App\Repository;

use App\Entity\Ordre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ordre>
 *
 * @method Ordre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ordre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ordre[]    findAll()
 * @method Ordre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrdreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ordre::class);
    }

    public function save(Ordre $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Ordre $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
} 