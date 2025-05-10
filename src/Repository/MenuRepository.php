<?php

namespace App\Repository;

use App\Entity\Menu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Translatable\TranslatableListener;

/**
 * 
 * MenuRepository : contient toutes les requêtes relatives aux Menus
 *
 * Ajouter vos requetes concerant les media ici.
 * 
 * @author Ace3i <mail@ace3i.com>
 * @copyright 2013 Ace3i
 * @link      http://www.utb.tg
 * 
 */
class MenuRepository extends ServiceEntityRepository
{
        public function __construct(ManagerRegistry $registry)
        {
                parent::__construct($registry, Menu::class);
        }

        /**
         * Methode qui recupere les menus parent      
         *      
         * @param <integer> $groupe groupe auquel appartiennent les menus parents
         *       
         */
        public function findMenusParent($groupe, $locale)
        {
                return $this->createQueryBuilder('m')
                        ->innerJoin('m.groupeMenu', 'g')
                        ->where('g.id = :groupe')
                        ->andWhere('m.parent IS NULL')
                        ->setParameter('groupe', $groupe)
                        ->getQuery()
                        ->setHint(
                                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
                                'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
                        )
                        ->setHint(
                                TranslatableListener::HINT_TRANSLATABLE_LOCALE,
                                $locale
                        )
                        ->getResult();
        }

        public function findOneMenuByLocale($id, $locale)
        {
                return $this->createQueryBuilder('m')
                        ->select('m.id', 'm.libMenu', 'm.typeMenu', 'm.urlExterneMenu')
                        ->innerJoin('m.groupeMenu', 'g')
                        ->where('m.id = :id')
                        ->setParameter('id', $id)
                        ->getQuery()
                        ->setHint(
                                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
                                'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
                        )
                        ->setHint(
                                TranslatableListener::HINT_TRANSLATABLE_LOCALE,
                                $locale
                        )
                        ->getResult();
        }

        public function findMenuByLocale($id, $locale)
        {
                $query = $this->createQueryBuilder('m')
                        ->select('m.id', 'm.libMenu', 'm.typeMenu', 'm.urlExterneMenu')
                        ->innerJoin('m.groupeMenu', 'g')
                        ->where('g.id = :id')
                        ->setParameter('id', $id)
                        ->getQuery()
                        ->setHint(
                                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
                                'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
                        )
                        ->setHint(
                                TranslatableListener::HINT_TRANSLATABLE_LOCALE,
                                $locale
                        )
                        ->getResult();
                return $query;
        }

        /**
         * Methode qui recupere les menus parent      
         *      
         * @param <integer> $groupe groupe auquel appartiennent les menus parents
         *       
         */
        public function findParent($locale)
        {
                $query = $this->createQueryBuilder('m')
                        ->where('m.parent IS NULL')
                        ->groupBy('m.id')
                        ->getQuery()
                        ->setHint(
                                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
                                'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
                        )
                        ->setHint(
                                TranslatableListener::HINT_TRANSLATABLE_LOCALE,
                                $locale
                        )
                        ->getResult();
                return $query;
        }

        /**
         * Methode qui recupere les menus enfants      
         *      
         * @param <integer> $idParents auquel appartiennent les menus enfants
         *       
         */
        public function findMenuFils($idParent, $locale)
        {
                $query = $this->createQueryBuilder('m')
                        ->where('m.parent = :idparent')
                        ->groupBy('m.id')
                        ->setParameter('idparent', $idParent)
                        ->getQuery()
                        ->setHint(
                                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
                                'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
                        )
                        ->setHint(
                                TranslatableListener::HINT_TRANSLATABLE_LOCALE,
                                $locale
                        )
                        ->getResult();
                return $query;
        }

        /**
         * Methode qui recupere un type de menu suivant une cle passee 
         *      
         * @param <integer> $key cle du tableau determinant le type de menu dans un tableau associatif (cle=>valeur)
         *       
         */
        public function getTextTypeMenu($key)
        {
                $listeType = array(
                        "0" => " ",
                        "1" => "Accueil",
                        "2" => "Articles de rubrique",
                        "3" => "Liste ou arborescence de rubrique",
                        "4" => "Rubrique",
                        "5" => "Article",
                        "6" => "Lien vers squelette de site",
                        "7" => "Lien vers Page Externe",
                );
                return $listeType[$key];
        }

        /**
         * Methode qui recupere une image type (son nom) suivant une valeur clee passee 
         *      
         * @param <integer> $key cle du tableau determinant le type d'image dans un tableau associatif (cle=>valeur)
         *       
         */
        public function getImageTypeMenu($key)
        {
                $listeImage = array(
                        "0" => "menus_objet.png",
                        "1" => "menus_accueil.png",
                        "2" => "menus_rubriques.png",
                        "3" => "menus_articles_rubrique.png",
                        "4" => "menus_objet.png",
                        "5" => "icon-24-article.png",
                        "6" => "menus_page_speciale.png",
                        "7" => "menus_lien.png",
                        "8" => "menus_page_speciale.png",
                        "9" => "menus_lien.png",
                );
                return $listeImage[$key];
        }

        public function getAllMediasMenu($idrubrique, $locale)
        {
                return $this->createQueryBuilder('m')
                        ->select(
                                'm.id',
                                'm.libMenu',
                                'm.urlExterneMenu',
                                'rub.nomRubrique',
                                'rub.descriptionRubrique'
                        )
                        ->innerJoin('App\Entity\Rubrique', 'rub')
                        ->where('rub.id = :idrub')
                        ->setParameter('idrub', $idrubrique)
                        ->getQuery()
                        ->setHint(
                                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
                                'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
                        )
                        ->setHint(
                                TranslatableListener::HINT_TRANSLATABLE_LOCALE,
                                $locale
                        )
                        ->getResult();
        }
}
