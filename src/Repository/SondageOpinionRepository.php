<?php

namespace App\Repository;

use App\Entity\SondageOpinion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SondageOpinion>
 *
 * @method SondageOpinion|null find($id, $lockMode = null, $lockVersion = null)
 * @method SondageOpinion|null findOneBy(array $criteria, array $orderBy = null)
 * @method SondageOpinion[]    findAll()
 * @method SondageOpinion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SondageOpinionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SondageOpinion::class);
    }

    public function save(SondageOpinion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SondageOpinion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
} 