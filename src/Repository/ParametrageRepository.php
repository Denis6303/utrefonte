<?php

namespace App\Repository;

use App\Entity\Parametrage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Parametrage>
 *
 * @method Parametrage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Parametrage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Parametrage[]    findAll()
 * @method Parametrage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParametrageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parametrage::class);
    }

    public function save(Parametrage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Parametrage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
} 