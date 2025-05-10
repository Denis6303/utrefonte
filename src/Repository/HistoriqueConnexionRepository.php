<?php

namespace App\Repository;

use App\Entity\HistoriqueConnexion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HistoriqueConnexion>
 *
 * @method HistoriqueConnexion|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoriqueConnexion|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoriqueConnexion[]    findAll()
 * @method HistoriqueConnexion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoriqueConnexionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistoriqueConnexion::class);
    }

    /**
     * Cette methode retourne l'id max des historiques lies a un abonne/utilisateur
     * 
     * @param int $idconcerne : identifiant de soit abonne soit utilisateur
     * @param int $type : type pour savoir si c'est un abonne(0) ou un utilisateur(1)
     * @return int: numero maximal de connexion
     */
    public function getMaxHistorique($idconcerne, $type)
    {
        //0 pr abonné et 1 pr utilisateur
        $sql = '';
        ($type == 0) ?
            $sql = 'SELECT  max(h.id)                                                          
              FROM   utbClientBundle:HistoriqueConnexion h 
                     INNER JOIN h.abonne a 
              WHERE  a.id = :id and h.dateFin is null and h.dateDeb >= CURRENT_DATE()
              GROUP  BY a.id ' :
            $sql = 'SELECT  max(h.id)                                                          
              FROM   utbClientBundle:HistoriqueConnexion h 
                     INNER JOIN h.utilisateur u 
              WHERE   u.id = :id and h.dateFin is null and h.dateDeb >= CURRENT_DATE() 
              GROUP BY u.id';

        $query = $this->_em->createQuery($sql)
            ->setParameter('id', $idconcerne);
        try {
            $resultat = $query->getSingleScalarResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            $resultat = 0;
        }
        return $resultat;
    }

    /**
     * Cette methode retourne la liste des historiques lies a un abonne/utilisateur
     * 
     * @param int $idconcerne : identifiant de soit abonne soit utilisateur
     * @param int $type : type pour savoir si c'est un abonne(0) ou un utilisateur(1)
     * @param int $limit : nombre d'historiques retournees
     * @param int $total : nombre total d'historiques 
     * @param int $page : numero de page en cours
     * @param int $articles_per_page :  nombre d'historique par page
     * @return int
     */
    public function getListeHistoriqueByType($idconcerne, $type, $limit, $total, $page, $articles_per_page)
    {

        $sql = '';
        $resultat = null;
        $query = null;
        ($type == 0) ?
            $sql = 'SELECT h.id ,h.lieu,h.dateDeb,h.dateFin,h.duree                                                           
              FROM   utbClientBundle:HistoriqueConnexion h     
                     INNER JOIN h.abonne a 
              WHERE  a.id = :id 
              ORDER  BY h.id DESC ' :
            $sql = 'SELECT h.id ,h.lieu,h.dateDeb,h.dateFin,h.duree                                                          
              FROM   utbClientBundle:HistoriqueConnexion h 
                     INNER JOIN h.utilisateur u 
              WHERE  u.id = :id                
              ORDER  BY h.id DESC ';

        $query = $this->_em->createQuery($sql);
        $query->setParameter('id', $idconcerne);

        $limit == 0 ? $query->setMaxResults($articles_per_page) : $query->setMaxResults($limit);
        $query->setFirstResult(($page * $articles_per_page) - $articles_per_page);

        try {
            $resultat = $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            $resultat = 0;
        }
        return $resultat;
    }

    /*
     * Que pour les abonnés
     */

    /**
     * Cette methode retourne la liste des historique d'un abonne
     * 
     * @param int $idconcerne : id de l'abonne uniquement
     * @param int $limit : nombre d'historiques retournees
     * @param int $total : nombre total d'historiques 
     * @param int $page : numero de page en cours
     * @param int $articles_per_page : nombre d'historiques par page
     * @return array: tableau d'historiques
     */
    public function getListeHistoriqueCompteByType($idconcerne, $limit, $total, $page, $articles_per_page)
    {

        $sql = '';
        $resultat = null;
        $query = null;

        $sql = 'SELECT h.id ,h.lieu,h.dateDeb,h.dateFin,h.duree                                                           
              FROM   utbClientBundle:HistoriqueConnexion h     
                     INNER JOIN h.abonne a 
              WHERE  a.id = :id 
              ORDER  BY h.id DESC ';

        $query = $this->_em->createQuery($sql)
            ->setParameter('id', $idconcerne);

        $limit == 0 ? $sql .= $query->setMaxResults($articles_per_page) : $query->setMaxResults($limit);

        $query->setFirstResult(($page * $articles_per_page) - $articles_per_page);


        try {
            $resultat = $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            $resultat = 0;
        }
        return $resultat;
    }

    /**
     * Cette methode retourne la liste des historique d'un abonne ou d'un utilisateur
     * 
     * @param int $idconcerne : id de l'abonne/utilisateur 
     * @param int $type : type pour savoir si c'est un abonne(0) ou un utilisateur(1)
     * @param int $limit : nombre d'historiques retournees
     * @return int : Nombre d'historique
     */
    public function getTotalHistorique($idconcerne, $type, $limit)
    {

        $sql = '';
        $resultat = null;
        $query = null;
        ($type == 0) ?
            $sql = 'SELECT h.id ,h.lieu,h.dateDeb,h.dateFin,h.duree                                                           
              FROM   utbClientBundle:HistoriqueConnexion h     
                     INNER JOIN h.abonne a 
              WHERE  a.id = :id 
              ORDER  BY h.id DESC ' :
            $sql = 'SELECT h.id ,h.lieu,h.dateDeb,h.dateFin,h.duree                                                          
              FROM   utbClientBundle:HistoriqueConnexion h 
                     INNER JOIN h.utilisateur u 
              WHERE  u.id = :id                
              ORDER  BY h.id DESC ';

        $query = $this->_em->createQuery($sql)
            ->setParameter('id', $idconcerne);
        $limit == 0 ? $sql .= '' : $query->setMaxResults($limit);
        try {
            $resultat = $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            $resultat = 0;
        }
        return count($resultat);
    }

    /**
     * Cette methode retourne l'historique maximale des dernieres historiques
     * 
     * @param int $idconcerne : identifiant de l'abonne/utilisateur
     * @param int $type : type pour savoir si c'est un abonne(0) ou un utilisateur(1)
     * @return int: Numero de l'historique maximale
     */
    public function getMaxLastHistorique($idconcerne, $type)
    {
        //0 pr abonné et 1 pr utilisateur
        $sql = '';
        ($type == 0) ?
            $sql = 'SELECT  max(h.id)                                                          
              FROM   utbClientBundle:HistoriqueConnexion h 
                     INNER JOIN h.abonne a 
              WHERE  a.id = :id 
              GROUP  BY a.id ' :
            $sql = 'SELECT  max(h.id)                                                          
              FROM   utbClientBundle:HistoriqueConnexion h 
                     INNER JOIN h.utilisateur u 
              WHERE   u.id = :id  
              GROUP BY u.id';

        $query = $this->_em->createQuery($sql)
            ->setParameter('id', $idconcerne);
        try {
            $resultat = $query->getSingleScalarResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            $resultat = 0;
        }
        return $resultat;
    }


    public function getAnneeHisto($limitan)
    {
        $sql = 'SELECT distinct substring(h.dateDeb,1,4)  as annee                             
                FROM   utbClientBundle:HistoriqueConnexion h 
                ORDER BY h.dateDeb DESC';

        $qan = $this->_em->createQuery($sql);
        $limitan == 0 ? $sqlan .= '' : $qan->setMaxResults($limitan);
        return $qan->getResult();
    }




    public function tableauHistorique($limitan, $limit, $annee, $mois)
    {

        $sqlan = '';
        $sqlmois = '';
        $sqlliste = '';

        /* debut determination des requetes a executer  */
        $resultat = array();
        $sqlan = 'SELECT distinct substring(h.dateDeb,1,4)  as annee                             
                  FROM   utbClientBundle:HistoriqueConnexion h ';

        $sqlmois  = 'SELECT distinct substring(h.dateDeb,6,2)  as mois                                                       
                    FROM   utbClientBundle:HistoriqueConnexion h 
                    WHERE  substring(h.dateDeb,1,4) = :an  ';

        $sqlliste = 'SELECT h                                                     
                     FROM   utbClientBundle:HistoriqueConnexion h 
                     WHERE  substring(h.dateDeb,1,4) = :an AND substring(h.dateDeb,6,2) = :mois     
                     ORDER BY h.dateDeb DESC  ';
        /* fin determination des requetes a executer */

        /* debut determination des annees de l'historique  */
        $annee == 0 ? $sqlan .= ''   : $sqlan  .= ' WHERE  substring(h.dateDeb,1,4) = :an ';
        $mois == 0 ?  $sqlmois .= '' : $sqlmois .= ' AND substring(h.dateDeb,6,2) = :mois ';

        $sqlan .= ' ORDER BY h.dateDeb DESC ';
        $sqlmois .= ' ORDER BY h.dateDeb DESC ';

        $qan = $this->_em->createQuery($sqlan);

        $annee == 0 ? $sqlan .= '' : $qan->setParameter('an', $annee);

        $limitan == 0 ? $sqlan .= '' : $qan->setMaxResults($limitan);
        $resultat['an'] = $qan->getResult();
        /* fin determination des annees de l'historique  */

        /* Debut determination des mois par annee de l'historique  */
        if (is_array($resultat['an']) && (count($resultat['an']) != 0) && ($resultat['an'] != null)) {
            foreach ($resultat['an'] as $key => $value) {
                $qmois = null;
                $param = null;
                $qmois = $this->_em->createQuery($sqlmois);
                $mois == 0 ? $param = array('an' => (int) $value['annee']) : $param = array('an' => (int) $value['annee'], 'mois' => $mois);
                $qmois->setParameters($param);
                $resultat['lesmois'] = $qmois->getResult();

                /* debut determination liste de $limit dernier(s) enregistrement(s) l'historique  */
                if (is_array($resultat['lesmois']) && (count($resultat['lesmois']) != 0) && ($resultat['lesmois'] != null)) {
                    foreach ($resultat['lesmois'] as $cle => $valeur) {
                        $qliste = null;
                        $qliste = $this->_em->createQuery($sqlliste);
                        $qliste->setParameters(array('an' => $value['annee'], 'mois' => $valeur['mois']));
                        if ($mois == 0) {
                            $limit == 0 ? $sqlliste .= '' : $qliste->setMaxResults($limit);
                        }
                        $resultat[$valeur['mois']]['liste'] = $qliste->getResult();
                    }
                }
            }
            /* fin determination liste de $limitdernier(s) enregistrement(s) l'historique  */
        }
        /* fin determination des mois par annee de l'historique  */
        //var_dump($resultat );
        return $resultat;
    }


    public function getListeHistorik($abonneutil, $deb, $fin, $typecon, $mois, $an, $page, $articles_per_page)
    {

        $param = array();

        if ($an != 0 && $mois != 0) {
            (strlen($deb) == 1) ? $deb = '0' . (string)$deb : $deb = (string)$deb;
            (strlen($fin) == 1) ? $fin = '0' . (string)$fin : $fin = (string)$fin;

            $unedatefin = new \DateTime();
            $fin = $unedatefin->setDate((int)$an, (int)$mois, (int)$fin);
            $fin = $unedatefin->format('Y-m-d');

            $unedatedebut = new \DateTime();
            $deb = $unedatedebut->setDate((int)$an, (int)$mois, (int)$deb);
            $deb = $unedatedebut->format('Y-m-d');
        }
        //Make a Select query
        $sql = 'SELECT h                                                       
                     FROM   utbClientBundle:HistoriqueConnexion h ';
        if ($typecon == 1) {
            $sql .= ' INNER JOIN utbClientBundle:abonne a ';
        } elseif ($typecon == 2) {
            $sql .= ' INNER JOIN utbClientBundle:utilisateur u ';
        }

        $sql .= 'WHERE  1 = 1 ';

        /*($an==0)?   $sql.='': $sql .= ' AND substring(h.dateDeb,1,4) = :an ';
        ($mois==0)? $sql.='': $sql .= ' AND substring(h.dateDeb,6,2) = :mois   ';*/

        if ($typecon == 1) {
            ($abonneutil == 0) ? $sql .= '' : $sql .= ' AND ( (a.id = :connecte) OR (a.nomPrenom like :nomprenom ) OR (a.username like :user) )  ';
        } elseif ($typecon == 2) {
            ($abonneutil == 0) ? $sql .= '' : $sql .= ' AND ( (u.id = :connecte) OR (u.nomPrenom like :nomprenom ) OR (u.username like :user) )  ';
        } else $sql .= '';

        //$date=

        /**  debut critère recherche  * */
        //var_dump($deb);exit;
        ((($deb != null)) && (($fin != null))) ?
            $sql .= " AND   h.dateDeb >= " . " '" . $deb . "' " . " " . " and  h.dateDeb <= " . " " . " '" . $fin . "' " : $sql .= '';
        # $sql.=" AND  h.dateDeb >= "  . $deb . " ' " . " and h.dateDeb <= " . " " . $fin . " " : $sql.='';

        $sql .= " ORDER BY h.dateDeb ";


        if ($abonneutil != 0 && $typecon != 0) {
            $param['connecte'] = $abonneutil;
            $param['nomprenom'] = $abonneutil + '%';
            $param['user'] = $abonneutil + '%';
        }

        /* ($an==0)?   $sql.='':  $param['an']=$an;
        ($mois==0)? $sql.='':  $param['mois']=$mois;*/

        /** Fin parametres et valeur * */
        $query = $this->_em->createQuery($sql);
        $query->setParameters($param);
        $query->setFirstResult(($page * $articles_per_page) - $articles_per_page);
        $query->setMaxResults($articles_per_page);
        // var_dump($sql);var_dump($param);exit;
        return $query->getResult();
    }

    public function getListeHistorikTotal($abonneutil, $deb, $fin, $typecon, $mois, $an)
    {

        $param = array();

        if ($an != 0 && $mois != 0) {
            (strlen($deb) == 1) ? $deb = '0' . (string)$deb : $deb = (string)$deb;
            (strlen($fin) == 1) ? $fin = '0' . (string)$fin : $fin = (string)$fin;

            $unedatefin = new \DateTime();
            $fin = $unedatefin->setDate((int)$an, (int)$mois, (int)$fin);
            $fin = $unedatefin->format('Y-m-d');

            $unedatedebut = new \DateTime();
            $deb = $unedatedebut->setDate((int)$an, (int)$mois, (int)$deb);
            $deb = $unedatedebut->format('Y-m-d');
        }
        //Make a Select query
        $sql = 'SELECT h                                                       
                     FROM   utbClientBundle:HistoriqueConnexion h ';
        if ($typecon == 1) {
            $sql .= ' INNER JOIN utbClientBundle:abonne a ';
        } elseif ($typecon == 2) {
            $sql .= ' INNER JOIN utbClientBundle:utilisateur u ';
        }

        $sql .= 'WHERE  1 = 1 ';

        /*($an==0)?   $sql.='': $sql .= ' AND substring(h.dateDeb,1,4) = :an ';
        ($mois==0)? $sql.='': $sql .= ' AND substring(h.dateDeb,6,2) = :mois   ';*/

        if ($typecon == 1) {
            ($abonneutil == 0) ? $sql .= '' : $sql .= ' AND ( (a.id = :connecte) OR (a.nomPrenom like :nomprenom ) OR (a.username like :user) )  ';
        } elseif ($typecon == 2) {
            ($abonneutil == 0) ? $sql .= '' : $sql .= ' AND ( (u.id = :connecte) OR (u.nomPrenom like :nomprenom ) OR (u.username like :user) )  ';
        } else $sql .= '';

        //$date=

        /**  debut critère recherche  * */
        //var_dump($deb);exit;
        ((($deb != null)) && (($fin != null))) ?
            $sql .= " AND   h.dateDeb >= " . " '" . $deb . "' " . " " . " and  h.dateDeb <= " . " " . " '" . $fin . "' " : $sql .= '';
        # $sql.=" AND  h.dateDeb >= "  . $deb . " ' " . " and h.dateDeb <= " . " " . $fin . " " : $sql.='';

        $sql .= " ORDER BY h.dateDeb ";


        if ($abonneutil != 0 && $typecon != 0) {
            $param['connecte'] = $abonneutil;
            $param['nomprenom'] = $abonneutil + '%';
            $param['user'] = $abonneutil + '%';
        }

        /* ($an==0)?   $sql.='':  $param['an']=$an;
        ($mois==0)? $sql.='':  $param['mois']=$mois;*/

        /** Fin parametres et valeur * */
        $query = $this->_em->createQuery($sql);
        $query->setParameters($param);

        // var_dump($sql);var_dump($param);exit;
        return $query->getResult();
    }
}
