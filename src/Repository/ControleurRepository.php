<?php

namespace App\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * 
 * ControleurRepository pour la gestion des requetes liees aux controleurs
 *
 * @author Ace3i <mail@ace3i.com>
 * @copyright 2013 Ace3i
 * @link      http://www.utb.tg
 * 
 */
class ControleurRepository extends EntityRepository {

    public function findAllAdmin($locale) {
        //Make a Select query 
        $query = $this->_em->createQuery('SELECT c.id as id, c.nomControleur, c.description 
                                         FROM utbAdminBundle:Controleur c
                                         WHERE c.client=:client');
        $query->setParameters(array('client' => 0));

        $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        // Force the locale
        $query->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale
        );
        return $query->getResult();
    }

    public function findAllClient($locale) {
        //Make a Select query 
        $query = $this->_em->createQuery('SELECT c.id as id, c.nomControleur, c.description 
                                         FROM utbAdminBundle:Controleur c
                                         WHERE c.client=:client');
        $query->setParameters(array('client' => 1));

        $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        // Force the locale
        $query->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale
        );
        return $query->getResult();
    }

}
