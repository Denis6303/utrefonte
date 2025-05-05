<?php

namespace App\Repository;

use App\Entity\Devise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Devise>
 *
 * @method Devise|null find($id, $lockMode = null, $lockVersion = null)
 * @method Devise|null findOneBy(array $criteria, array $orderBy = null)
 * @method Devise[]    findAll()
 * @method Devise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviseRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Devise::class);
  }

  /**
   * @param string $locale
   * @return array
   */
  public function findAllDeviseByLocale($locale)
  {
    $qb = $this->createQueryBuilder('d')
      ->select(
        'd.id as id',
        'd.libDevise as libDevise',
        'd.symboleDevise as symboleDevise',
        'd.etatDevise as etat',
        'd.tauxDevise as taux'
      )
      ->orderBy('d.id', 'DESC');

    $query = $qb->getQuery();
    $query->setHint(
      \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
      'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
    );
    $query->setHint(
      \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE,
      $locale
    );

    return $query->getResult();
  }

  /**
   * @param int $id
   * @return array
   */
  public function findOneDevise($id)
  {
    return $this->createQueryBuilder('d')
      ->where('d.id = :id')
      ->setParameter('id', $id)
      ->getQuery()
      ->getResult();
  }

  /**
   * Methode permettant de voir l'existence d'une devise locale
   * 
   * @param bool $islocale
   * @return array
   */
  public function getTestDeviseLocale($islocale)
  {
    return $this->createQueryBuilder('d')
      ->where('d.siLocale = :paramlocal')
      ->andWhere('d.affiche = 1')
      ->setParameter('paramlocal', $islocale)
      ->getQuery()
      ->getResult();
  }
}
