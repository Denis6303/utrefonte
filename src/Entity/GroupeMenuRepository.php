<?php

namespace App\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * 
 * GroupeMenuRepository : contient toutes les requetes concernant les groupes de menu (GroupeMenu)
 *
 * Ajouter vos requetes concerant les groupes de menu ici.
 * 
 * @author Ace3i <mail@ace3i.com>
 * @copyright 2013 Ace3i
 * @link      http://www.utb.tg
 * 
 */
class GroupeMenuRepository extends EntityRepository {

    public function findByLocale($locale) {

        $query = $this->_em->createQuery('SELECT g FROM utbAdminBundle:groupeMenu g 
                                           ');

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
