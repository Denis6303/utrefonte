<?php

namespace App\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * 
 * ActionRepository  
 * 
 * 
 * 
 * 
 * @author Ace3i <mail@ace3i.com>
 * @copyright 2013 Ace3i
 * @link      http://www.utb.tg
 * 
 * 
 */
class ActionRepository extends EntityRepository {

    public function getActionsByModule($idModule) {
        $qb = $this->createQueryBuilder('a')
                ->leftJoin('a.module', 'm')
                ->addSelect('m')
                ->orderBy('m.libmodule', 'ASC')
                ->where('a.module = :id')
                ->setParameter('id', $idModule);

        return $qb->getQuery()
                        ->getResult();
    }

    public function findAllAdmin($locale) {
        //Make a Select query 
        $query = $this->_em->createQuery('SELECT a.id as id, a.libAction, a.descriptionAction 
                                         FROM utbAdminBundle:Action a
                                         WHERE a.client=:client');
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
        $query = $this->_em->createQuery('SELECT a.id as id, a.libAction, a.descriptionAction 
                                         FROM utbAdminBundle:Action a
                                         WHERE a.client=:client');
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

    public function findOneClient($idaction) {
        //Make a Select query 
        $query = $this->_em->createQuery('SELECT a.id as id, a.libAction, a.descriptionAction 
                                         FROM utbAdminBundle:Action a
                                         WHERE a.client=:client AND a.id=:idaction');
        $query->setParameters(array('client' => 1, 'idaction' => $idaction));

        return $query->getResult();
    }

}
