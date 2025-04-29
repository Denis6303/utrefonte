<?php

namespace App\Repository;

use App\Entity\NatureDoc;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NatureDoc>
 *
 * @method NatureDoc|null find($id, $lockMode = null, $lockVersion = null)
 * @method NatureDoc|null findOneBy(array $criteria, array $orderBy = null)
 * @method NatureDoc[]    findAll()
 * @method NatureDoc[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NatureDocRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NatureDoc::class);
    }

    public function save(NatureDoc $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(NatureDoc $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
} 