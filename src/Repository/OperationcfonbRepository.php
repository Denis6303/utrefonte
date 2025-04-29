<?php

namespace App\Repository;

use App\Entity\Operationcfonb;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Operationcfonb>
 *
 * @method Operationcfonb|null find($id, $lockMode = null, $lockVersion = null)
 * @method Operationcfonb|null findOneBy(array $criteria, array $orderBy = null)
 * @method Operationcfonb[]    findAll()
 * @method Operationcfonb[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OperationcfonbRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Operationcfonb::class);
    }

    public function save(Operationcfonb $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Operationcfonb $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
} 