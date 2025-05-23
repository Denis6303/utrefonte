<?php

namespace App\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * AgenceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AgenceRepository extends EntityRepository
{
    
    public function getListeAgence($total, $page, $articles_per_page) {
        $param = array();
        
        $sql = "SELECT DISTINCT a.codeAgence,a.etatAgence,a.telAgence,a.adresseAgence,a.dateCreation 
                FROM utbClientBundle:Agence a";
        $sql.=" WHERE a.suppr != :suppr";

        /**  debut parametres  * */  
            $param['suppr'] = 0;
        /** Fin parametres et valeur * */
        $sql.='  ORDER BY a.dateCreation DESC';

        $query = $this->_em->createQuery($sql);

        //$limit==0 ? $sql.='' :  $query->setMaxResults($limit); 

        $query->setParameters($param);
        $query->setFirstResult(($page * $articles_per_page) - $articles_per_page);
        $query->setMaxResults($articles_per_page);
        return $query->getResult();
    }
    
}
