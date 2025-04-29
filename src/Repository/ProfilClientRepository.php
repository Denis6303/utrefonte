<?php

namespace App\Repository;

use App\Entity\ProfilClient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProfilClient>
 *
 * @method ProfilClient|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProfilClient|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProfilClient[]    findAll()
 * @method ProfilClient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfilClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProfilClient::class);
    }

    public function save(ProfilClient $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ProfilClient $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
} 