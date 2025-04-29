<?php

namespace App\Repository;

use App\Entity\TypeCadre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeCadre>
 *
 * @method TypeCadre|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeCadre|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeCadre[]    findAll()
 * @method TypeCadre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeCadreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeCadre::class);
    }

    public function save(TypeCadre $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TypeCadre $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
} 