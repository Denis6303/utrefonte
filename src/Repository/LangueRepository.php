<?php

namespace App\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * LangueRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LangueRepository extends EntityRepository {

    public function getLangueActifSite($locale) {
        //requête de recherche 
        $sql = '';
        $sql = 'SELECT l FROM utbAdminBundle:Langue l
          WHERE  l.langueEtat =1';


        $q1 = null;
        $q1 = $this->_em->createQuery($sql);
        $q1->setparameter('type', $type);

        /*         * ************* Debut doctrine Extensions pour la gestion de langue*********** */
        /*     $q1->setHint(
          \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
          'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
          );

          // Force the locale
          $q1->setHint(
          \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE,
          $locale
          ); */
        /*         * *********** Fin doctrine Extensions ********** */

        $count = 0;
        try {
            $resultat = $q1->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            
        }
        return $resultat;
    }

    public function getAutreLanguePrArticle($locale) {

        //initialisation
        $resultat = null;

        $sql = '';
        $sql = 'SELECT l.id;l.codeLangue,l.libLabgue,l. FROM utbAdminBundle:Langue l
			  WHERE  l.langueEtat=1 and l.codeLangue <> :code';


        $q1 = null;
        $q1 = $this->_em->createQuery($sql);
        $q1->setparameter('code', $locale);

        /*         * ************* Debut doctrine Extensions pour la gestion de langue*********** */
        /*     $q1->setHint(
          \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
          'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
          );

          // Force the locale
          $q1->setHint(
          \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE,
          $locale
          ); */
        /*         * *********** Fin doctrine Extensions ********** */

        $resultat = $q1->getResult();

        return $resultat;
    }

}
