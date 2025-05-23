<?php

namespace App\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * FondsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FondsRepository extends EntityRepository {

    /**
     * Cette lmethode retourne les comptes presents dans un fonds donne
     * 
     * @param int $idgestion : Identifiant du gestionnaire
     * @return array : Tableau de compte appartenant à ce fonds
     */
    public function findFondsGestionnaire($idgestion) {
        $query = $this->_em->createQuery('SELECT     f.id,f.etatFonds,u.nomPrenom, f.libFonds, f.codeFonds                                                
                                          FROM      utbClientBundle:Utilisateur u 
                                                    INNER JOIN u.fonds f                                                    
                                                    Where u.id =:id  ')
                ->setParameter('id', $idgestion);

        try {
            $count = $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            $count = null;
        }
        return $query->getResult();
    }
    
    /**
     * Cette methode retourne un tableau d'utilisateur
     * 
     * @param string $locale: pour la gestion multilangue
     * @return array: un tableau d'utilisateur(s)
     */
    public function findFonds() {
        //$articles_per_page = $this->container->getParameter('max_articles_on_listepage');
        //Make a Select query ---
        $query = $this->_em->createQuery('SELECT    f.id,f.etatFonds,u.nomPrenom, f.libFonds, f.codeFonds                                                
                                          FROM      utbClientBundle:Utilisateur u 
                                                    INNER JOIN u.fonds f 
                                          WHERE f.suppr=:suppr ORDER BY f.codeFonds DESC')
                ->setParameter('suppr', 0);
       
        return $query->getResult();
    }

}
