<?php

namespace App\Repository;

use App\Entity\Cadre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cadre>
 *
 * @method Cadre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cadre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cadre[]    findAll()
 * @method Cadre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CadreRepository extends ServiceEntityRepository
{
        public function __construct(ManagerRegistry $registry)
        {
                parent::__construct($registry, Cadre::class);
        }

        /**
         *  Methode pour avoir la liste des cadres et leur type sur la page listeCadre.html.twig 
         * 
         * Table(s):  TypeCadre, Cadre
         * 
         * @param string $locale : Variable passee pour gerer le multilingue sur le site
         * 
         * @return array return le  resultat d'une requete
         * 
         */
        public function findAllCadreByLocale($locale)
        {
                $qb = $this->createQueryBuilder('c')
                        ->select(
                                'c.id as id',
                                'c.libCadre as libCadre',
                                'c.contenuCadre as contenuCadre',
                                'c.etatCadre as etat',
                                't.id as idtype',
                                't.libTypeCadre as libtype',
                                'c.rubPointer as rubPointer'
                        )
                        ->join('c.typeCadre', 't')
                        ->orderBy('c.id', 'DESC');

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
         *  Methode pour avoir la liste des cadres et leur type sur la page listeCadre.html.twig 
         * 
         * Table(s):  TypeCadre, Cadre
         * 
         * @param string $locale : Variable passee pour gerer le multilingue sur le site
         * @param int $typeCadre : ID du type de cadre
         * 
         * @return array return le  resultat d'une requete
         * 
         */
        public function findAllCadreAccueil($locale, $typeCadre)
        {
                $qb = $this->createQueryBuilder('c')
                        ->select(
                                'c.id as id',
                                'c.libCadre as libCadre',
                                'c.contenuCadre as contenuCadre',
                                'c.etatCadre as etat',
                                't.id as idtype',
                                't.libTypeCadre as libtype',
                                'c.rubPointer as rubPointer',
                                'c.articlePointer'
                        )
                        ->join('c.typeCadre', 't')
                        ->where('t.id = :typeCadre')
                        ->andWhere('c.rubPointer != 0')
                        ->orderBy('c.id', 'DESC')
                        ->setParameter('typeCadre', $typeCadre);

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
         *  Methode pour avoir la liste des cadres et leur type sur la page listeCadre.html.twig 
         * 
         * Table(s):  TypeCadre, Cadre
         * 
         * @param string $locale : Variable passee pour gerer le multilingue sur le site
         * @param int $typeCadre : ID du type de cadre
         * 
         * @return array return le  resultat d'une requete
         * 
         */
        public function findAllCadreArticleAccueil($locale, $typeCadre)
        {
                $qb = $this->createQueryBuilder('c')
                        ->select(
                                'c.id as id',
                                'c.libCadre as libCadre',
                                'c.contenuCadre as contenuCadre',
                                'c.etatCadre as etat',
                                't.id as idtype',
                                't.libTypeCadre as libtype',
                                'c.rubPointer as rubPointer',
                                'c.articlePointer'
                        )
                        ->join('c.typeCadre', 't')
                        ->where('t.id = :typeCadre')
                        ->andWhere('c.articlePointer != 0')
                        ->orderBy('c.id', 'DESC')
                        ->setParameter('typeCadre', $typeCadre);

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
         * Methode pour avoir la liste des cadres et leur rubrique pour le dynamisme de la page d'accueil 
         * 
         * Table(s):  TypeCadre, Cadre
         * 
         * @param int $id : ID de la rubrique
         * @param string $locale : Variable passee pour gerer le multilingue sur le site
         * 
         * @return array return le  resultat d'une requete
         * 
         */
        public function afficherUneRubriqueLocale($id, $locale)
        {
                $qb = $this->createQueryBuilder('c')
                        ->select(
                                'c.id as id',
                                'c.libCadre as libCadre',
                                'c.contenuCadre as contenuCadre',
                                'c.etatCadre as etat',
                                't.id as idtype',
                                't.libTypeCadre as libtype'
                        )
                        ->join('c.typeCadre', 't')
                        ->where('c.rubPointer = :id')
                        ->orderBy('c.id', 'DESC')
                        ->setParameter('id', $id);

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
         * Pour verifier si l'emplacement contient des cadres avant de le supprimer
         * 
         * @param int $cond : ID de l'emplacement
         * 
         * @return array
         */
        public function findEmplacement($cond)
        {
                $qb = $this->createQueryBuilder('c')
                        ->select('e.id as id')
                        ->join('App\Entity\Emplacement', 'e')
                        ->where('e.id = :cond')
                        ->setParameter('cond', $cond);

                return $qb->getQuery()->getResult();
        }

        /**
         * @param int $id : ID du cadre
         * 
         * @return array
         */
        public function findOneCadre($id)
        {
                return $this->createQueryBuilder('c')
                        ->where('c.id = :id')
                        ->setParameter('id', $id)
                        ->getQuery()
                        ->getResult();
        }
}
