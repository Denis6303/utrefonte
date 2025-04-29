<?php

namespace App\Repository;

use App\Entity\Compte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Compte>
 *
 * @method Compte|null find($id, $lockMode = null, $lockVersion = null)
 * @method Compte|null findOneBy(array $criteria, array $orderBy = null)
 * @method Compte[]    findAll()
 * @method Compte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Compte::class);
    }

    public function save(Compte $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Compte $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findActiveAccounts(): array
    {
        return $this->findBy(['actif' => true]);
    }

    public function findInactiveAccounts(): array
    {
        return $this->findBy(['actif' => false]);
    }

    public function findAccountsByType(string $type): array
    {
        return $this->findBy(['type' => $type]);
    }

    public function findAccountsByUser(int $userId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.utilisateur = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function findAccountsWithBalance(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.solde > 0')
            ->getQuery()
            ->getResult();
    }
} 