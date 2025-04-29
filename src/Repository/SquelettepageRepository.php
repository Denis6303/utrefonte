<?php

namespace App\Repository;

use App\Entity\Squelettepage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Squelettepage>
 *
 * @method Squelettepage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Squelettepage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Squelettepage[]    findAll()
 * @method Squelettepage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SquelettepageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Squelettepage::class);
    }

    public function save(Squelettepage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Squelettepage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
} 