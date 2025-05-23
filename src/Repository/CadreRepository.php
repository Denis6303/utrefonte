<?php

namespace App\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CadreRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CadreRepository extends EntityRepository {

    /**
     *  Methode pour avoir la liste des cadres et leur type sur la page listeCadre.html.twig 
     * 
     * Table(s):  TypeCadre, Cadre
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le  resultat d'une requete
     * 
     */
    public function findAllCadreByLocale($locale) {
        //Make a Select query 
        $query = $this->_em->createQuery('SELECT c.id as id, c.libCadre as libCadre,
                                         c.contenuCadre as contenuCadre,c.etatCadre as etat,
                                         t.id as idtype, t.libTypeCadre as libtype, c.rubPointer as rubPointer
                                         FROM utbAdminBundle:Cadre c JOIN c.typeCadre t                                         
                                         ORDER BY id DESC');

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
     *  Methode pour avoir la liste des cadres et leur type sur la page listeCadre.html.twig 
     * 
     * Table(s):  TypeCadre, Cadre
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le  resultat d'une requete
     * 
     */
    public function findAllCadreAccueil($locale, $typeCadre) {
        //Make a Select query 
        $query = $this->_em->createQuery('SELECT c.id as id, c.libCadre as libCadre,
                                         c.contenuCadre as contenuCadre,c.etatCadre as etat,
                                         t.id as idtype, t.libTypeCadre as libtype, c.rubPointer as rubPointer, c.articlePointer
                                         FROM utbAdminBundle:Cadre c JOIN c.typeCadre t                                         
                                         WHERE t.id=:typeCadre AND c.rubPointer!=0 ORDER BY id DESC');
        $query->setParameter('typeCadre', $typeCadre);
        //var_dump($query);
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
     *  Methode pour avoir la liste des cadres et leur type sur la page listeCadre.html.twig 
     * 
     * Table(s):  TypeCadre, Cadre
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le  resultat d'une requete
     * 
     */
    public function findAllCadreArticleAccueil($locale, $typeCadre) {
        //Make a Select query 
        $query = $this->_em->createQuery('SELECT c.id as id, c.libCadre as libCadre,
                                         c.contenuCadre as contenuCadre,c.etatCadre as etat,
                                         t.id as idtype, t.libTypeCadre as libtype, c.rubPointer as rubPointer, c.articlePointer
                                         FROM utbAdminBundle:Cadre c JOIN c.typeCadre t                                         
                                         WHERE t.id=:typeCadre AND c.articlePointer!=0 ORDER BY id DESC');
        $query->setParameter('typeCadre', $typeCadre);
        // var_dump($query);
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
     * Methode pour avoir la liste des cadres et leur rubrique pour le dynamisme de la page d'accueil 
     * 
     * Table(s):  TypeCadre, Cadre
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le  resultat d'une requete
     * 
     */
    public function afficherUneRubriqueLocale($id, $locale) {
        //Make a Select query 
        $query = $this->_em->createQuery('SELECT c.id as id, c.libCadre as libCadre,
                                            c.contenuCadre as contenuCadre,c.etatCadre as etat,
                                            t.id as idtype, t.libTypeCadre as libtype
                                            FROM utbAdminBundle:Cadre c
                                            WHERE c.ru=:id
                                            ORDER BY id DESC');
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

    //Pour verifier si l'emplacement contient des cadres avant de le supprimer - 18/11/2013    
    public function findEmplacement($cond) {

        //Make a Select query
        $query = $this->_em->createQuery('SELECT e.id as id
            FROM utbAdminBundle:Emplacement e INNER JOIN e.cadre c WHERE e.id =:cond')
                ->setParameter('cond', $cond);
        return $query->getResult();
    }

    //19/11/2013
    public function findOneCadre($id) {
        //Make a Select query
        $query = $this->_em->createQuery('SELECT c FROM utbAdminBundle:Cadre c  WHERE c.id = :id ');
        $query->setParameter('id', $id);
        /*
          $query->setHint(
          \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
          'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
          );
          // Force the locale
          $query->setHint(
          \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE,$locale
          ); */
        return $query->getResult();
    }

}
