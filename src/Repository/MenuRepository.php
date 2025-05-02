<?php

namespace App\Entity;

use Doctrine\ORM\EntityRepository;

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
class MenuRepository extends EntityRepository {

    /**
     * Methode qui recupere les menus parent      
     *      
     * @param <integer> $groupe groupe auquel appartiennent les menus parents
     *       
     */
    public function findMenusParent($groupe, $locale) {

        $query = $this->_em->createQuery('SELECT m  FROM utbAdminBundle:menu m 
                                          INNER JOIN m.groupeMenu g WHERE  g.id =:groupe  
                                          AND m.idParentMenu =0 ');
        $query->setParameter('groupe', $groupe);

        $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        // Force the locale
        $query->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale
        );
        return $query->getResult();
    }

    public function findOneMenuByLocale($id, $locale) {

        $query = $this->_em->createQuery('SELECT m.id as id, m.libMenu as libMenu,m.idParentMenu  as idParentMenu,m.typeMenu  as typeMenu, m.urlExterneMenu  as urlExterneMenu FROM utbAdminBundle:menu m 
                                              INNER JOIN m.groupeMenu g WHERE  m.id =:id  
                                             ');
        $query->setParameter('id', $id);

        $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        // Force the locale
        $query->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale
        );
        return $query->getResult();
    }

    
    public function findMenuByLocale($id, $locale) {

        $query = $this->_em->createQuery('SELECT m.id as id, m.libMenu as libMenu,m.idParentMenu  as idParentMenu,m.typeMenu  as typeMenu, m.urlExterneMenu  as urlExterneMenu FROM utbAdminBundle:menu m 
                                              INNER JOIN m.groupeMenu g WHERE  g.id =:id  
                                             ');
        $query->setParameter('id', $id);

        $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        // Force the locale
        $query->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale
        );
        return $query->getResult();
    }

    
    /**
     * Methode qui recupere les menus parent      
     *      
     * @param <integer> $groupe groupe auquel appartiennent les menus parents
     *       
     */
    public function findParent($locale) {

        $query = $this->_em->createQuery('SELECT m  FROM utbAdminBundle:menu m 
                                          INNER JOIN m.groupeMenu g WHERE m.idParentMenu =0 
                                          GROUP  BY  m.idParentMenu');

        $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        // Force the locale
        $query->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale
        );
        return $query->getResult();
    }

    /**
     * Methode qui recupere les menus enfants      
     *      
     * @param <integer> $idParents auquel appartiennent les menus enfants
     *       
     */
    public function findMenuFils($idParent, $locale) {
        /* $query = $this->createQueryBuilder('menu')
          ->where('menu.idParentMenu =:idparent') */
        $query = null;
        $query = $this->_em->createQuery('SELECT m FROM utbAdminBundle:menu m 
                                          WHERE  m.idParentMenu =:idparent
                                          GROUP  BY m.idParentMenu ');
        $query->setParameter('idparent', $idParent);
        /* $query->setHint(
          \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
          );
          // Force the locale
          $query->setHint(
          \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale
          ); */

        $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker');
        // Force the locale
        $query->setHint(\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale);


        return $query->getResult();
    }

    /**
     * Methode qui recupere un type de menu suivant une cle passee 
     *      
     * @param <integer> $key cle du tableau determinant le type de menu dans un tableau associatif (cle=>valeur)
     *       
     */
    public function getTextTypeMenu($key) {

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
    public function getImageTypeMenu($key) {

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

    public function getAllMediasMenu($idrubrique, $locale) {


        $q1 = null;
        $q1 = $this->_em->createQuery('SELECT m.id, m.typeMedia, m.urlMedia, m.nomMedia ,
                                        m.descriptionMedia, r.nomRubrique , r.descriptionRubrique
                                        FROM utbAdminBundle:Rubrique r inner join r.medias m
                                        WHERE r.id =:idrub and m.typeMedia=3 
                                        ');
        $q1->setParameter('idrub', $idrubrique);
        $q1->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
// Force the locale
        $q1->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale
        );

        return $q1->getResult();
    }

}
