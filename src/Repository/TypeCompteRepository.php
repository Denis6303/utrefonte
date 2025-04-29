<?php

namespace App\Repository;

use App\Entity\TypeCompte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeCompte>
 *
 * @method TypeCompte|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeCompte|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeCompte[]    findAll()
 * @method TypeCompte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeCompteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeCompte::class);
    }

    public function save(TypeCompte $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TypeCompte $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
} 