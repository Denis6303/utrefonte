<?php

namespace App\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * 
 * ArticleRepository pour la gestion des requetes liees a l'article
 *
 * @author Ace3i <mail@ace3i.com>
 * @copyright 2013 Ace3i
 * @link      http://www.utb.tg
 * 
 */
class ArticleRepository extends EntityRepository {

    /**
     *  Methode pour avoir la liste des articles d'une rubrique
     * 
     * Table(s):  Rubrique
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * @param <integer> $type :   Variable pour avoir l'identifiant de la rubrique  dans laquelle se trouve l'article
     * 
     * @return <string> return le  resultat d'une requete
     * 
     */
    public function getListeByRubriqueLocale($type, $locale = 'en') {

        //Make a Select query
        $query = $this->_em->createQuery('SELECT r,a  FROM utbAdminBundle:Article a 
                                          INNER JOIN a.rubrique r WHERE  r.id =:type  
                                          AND a.corbeilleArticle=0 AND a.archiveArticle=0 AND trim(a.titreArticle) !=:vide ');
        $query->setParameters(array('type' => $type, 'vide' => ''));

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
     *  Methode pour avoir la liste des articles d'une rubrique
     * 
     * Table(s):  Rubrique
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * @param <integer> $type :   Variable pour avoir l'identifiant de la rubrique  dans laquelle se trouve l'article
     * 
     * @return <string> return le  resultat d'une requete
     * 
     */
    public function getListeByParentRubriqueLocale($type, $locale = 'en', $limit) {
        $param = array();
        //Make a Select query
        $sql = 'SELECT r.id as idrubrique,r.nomRubrique,a.titreArticle,a.introTexteArticle,a.descriptionArticle, a.typePre as typePre ,r.descriptionRubrique,m.urlMedia  FROM utbAdminBundle:Article a 
                                          INNER JOIN a.rubrique r INNER JOIN a.medias m WHERE  r.idparent =:type  AND a.statutArticle=4
                                          AND a.corbeilleArticle=0 AND a.archiveArticle=0 AND trim(a.titreArticle) !=:vide ';
        //$sql.=' GROUP BY r.id ';
        $param['type'] = $type;
        $param['vide'] = '';

        $query = $this->_em->createQuery($sql);
        $limit == 0 ? $sql.='' : $query->setMaxResults($limit);
        ;

        $query->setParameters($param);

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
     *  Methode pour avoir la liste des articles d'une rubrique
     * 
     * Table(s):  Rubrique
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * @param <integer> $type :   Variable pour avoir l'identifiant de la rubrique  dans laquelle se trouve l'article
     * 
     * @return <string> return le  resultat d'une requete
     * 
     */
    public function getListeByParentRubriqueAccueilLocale($type, $locale = 'en', $limit) {
        $param = array();
        //Make a Select query
        $sql = 'SELECT r.id as idrubrique,r.nomRubrique,a.titreArticle,a.introTexteArticle,a.descriptionArticle,m.urlMedia  FROM utbAdminBundle:Article a 
                                          INNER JOIN a.rubrique r INNER JOIN a.medias m WHERE  r.idparent =:type  AND a.statutArticle=4
                                          AND a.corbeilleArticle=0 AND a.archiveArticle=0 AND trim(a.titreArticle) !=:vide AND a.afficheAccueil=1 ';

        $param['type'] = $type;
        $param['vide'] = '';

        $query = $this->_em->createQuery($sql);
        $limit == 0 ? $sql.='' : $query->setMaxResults($limit);
        ;

        $query->setParameters($param);

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
     *  Methode pour avoir la liste des articles dans une rubrique sur la page oneRubrique.html.twig 
     * 
     * Table(s):  Rubrique, Article
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * @param <integer> $type :  Variable pour avoir l'identifiant de la rubrique  dans laquelle se trouve l'article
     * @param <integer> $page :  Variable intervenant dans la pagination
     * @param <integer> $total :  Nombre total d'article
     * @param <integer> $articles_per_page :  Nombre d'article par page
     * 
     * @return <string> return le  resultat d'une requete
     * 
     */
    public function findAllArticleByLocale($type, $locale, $total, $page, $articles_per_page) {
        //Make a Select query 
        $query = $this->_em->createQuery('SELECT a.id as id, a.titreArticle as titreArticle,
                                         a.descriptionArticle as descriptionArticle,a.introTexteArticle as introTexteArticle,a.afficheAccueil,
                                         a.articleDateAjout as articleDateAjout, a.articleAjoutPar as articleAjoutPar,
                                         a.statutArticle as statutArticle, r.id as idRubrique,a.archiveArticle As archiveArticle
                                         ,a.corbeilleArticle As corbeilleArticle
                                         FROM utbAdminBundle:Article a INNER JOIN a.rubrique r
                                         WHERE r.id =:type AND a.archiveArticle=:arch AND a.corbeilleArticle=:corb 
                                         and trim(a.titreArticle) !=:vide
                                         ORDER BY a.ordre,id DESC');
        $query->setParameters(array('type' => $type, 'arch' => 0, 'corb' => 0, 'vide' => ''));

        $query->setFirstResult(($page * $articles_per_page) - $articles_per_page);
        $query->setMaxResults($articles_per_page);

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
     *  Methode pour avoir la liste des articles sur la page listeArticle.html.twig 
     * 
     * Table(s):  Rubrique, Article, Media
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id :   Identifiant de l'article
     * 
     * @return <string> return le  resultat d'une requete 
     * 
     */
    public function findOneByLocale($id, $locale) {
        //Make a Select query
        $query = $this->_em->createQuery('SELECT a.id as id, a.titreArticle as titreArticle,a.referenceArticle as referenceArticle,a.urlArticle as urlArticle,
                                         a.descriptionArticle as descriptionArticle,a.introTexteArticle as introTexteArticle,a.articleAjoutPar as articleAjoutPar,
                                         a.articleDateAjout as articleDateAjout, a.typePre as typePre ,a.articleModifPar as articleModifPar,a.articleDateModif as articleDateModif,
                                         a.articleDateValide as articleDateValide,a.articlePubliePar as articlePubliePar,a.archiveArticle,
                                         a.articleValidePar as articleValidePar,a.articleDatePublie as articleDatePublie,a.corbeilleArticle,a.afficheDatePublie as afficheDatePublie,
                                         a.afficheAuteur as afficheAuteur,a.afficheReference as afficheReference, r.id as idRubrique,r.nomRubrique as nomRubrique ,m.id as idMedia, m.urlMedia as urlMedia,m.nomMedia as nomMedia,
                                         m.typeMedia as typeMedia, m.illustreImgMedia as illustreImgMedia,a.statutArticle as statutArticle
                                         FROM utbAdminBundle:Article a INNER JOIN a.medias m INNER JOIN a.rubrique r
                                         WHERE a.id = :id and m.illustreImgMedia =1 
                                         ORDER BY a.ordre,a.id DESC ');
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
     *  Methode pour avoir la liste des articles sur la page listeArticle.html.twig 
     * 
     * Table(s):  Rubrique, Article, Media
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id :   Identifiant de l'article
     * 
     * @return <string> return le  resultat d'une requete 
     * 
     */
    public function findOneByAccueil($id, $locale) {
        //Make a Select query
        $query = $this->_em->createQuery('SELECT a.id as id, a.titreArticle as titreArticle,a.referenceArticle as referenceArticle,a.urlArticle as urlArticle,
                                         a.descriptionArticle as descriptionArticle ,a.introTexteArticle as introTexteArticle,r.typePresentation,p.id as idparent,                                     
                                         r.id as idRubrique,r.nomRubrique as nomRubrique ,m.id as idMedia, m.urlMedia as urlMedia,m.nomMedia as nomMedia,
                                         m.typeMedia as typeMedia, m.illustreImgMedia as illustreImgMedia,a.statutArticle as statutArticle,p.idgrandparent                                         
                                         FROM utbAdminBundle:Article a INNER JOIN a.medias m INNER JOIN a.rubrique r INNER JOIN r.idparent p
                                         WHERE a.id = :id and m.illustreImgMedia =1  AND a.statutArticle = 4
                                         
                                         ORDER BY a.ordre,a.id DESC ');
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

    //pour les cadres
    public function findOneByLocale2($id, $locale) {
        //Make a Select query
        $query = $this->_em->createQuery('SELECT a.id as id,a.titreArticle as titreArticle,a.introTexteArticle,a.descriptionArticle as descriptionArticle FROM utbAdminBundle:Article a WHERE a.id = :id');
        $query->setParameter('id', $id);

        $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        // Force the locale
        $query->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale
        );
        return $query->getSingleResult();
    }

    /**
     *  Methode pour avoir la liste des articles de la meme rubrqiue a retrouve sur le twig oneArticle.html.twig 
     * 
     * Table(s):  Rubrique, Article
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id :   Identifiant de l'article
     * @param <integer> $type : Identifiant de la rubrique de l'article
     * 
     * @return <string> return le  resultat d'une requete 
     * 
     */
    public function listeMemeRubriqueLocale($type, $locale = 'en', $id = "") {
        //Make a Select query
        $query = $this->_em->createQuery('SELECT a.id as id, a.titreArticle as titreArticle,a.referenceArticle as referenceArticle,a.urlArticle as urlArticle,
                                            a.descriptionArticle as descriptionArticle,a.introTexteArticle as introTexteArticle,a.articleAjoutPar as articleAjoutPar,
                                            a.articleDateAjout as articleDateAjout,a.articleModifPar as articleModifPar,a.articleDateModif as articleDateModif,
                                            a.articleDateValide as articleDateValide,a.articlePubliePar as articlePubliePar,
                                            a.articleValidePar as articleValidePar,a.articleDatePublie as articleDatePublie,a.afficheDatePublie as afficheDatePublie,
                                            a.afficheAuteur as afficheAuteur, a.typePre as typePre ,a.afficheReference as afficheReference, r.id as idRubrique,r.nomRubrique as nomRubrique ,m.id as idMedia, m.urlMedia as urlMedia,m.nomMedia as nomMedia,
                                            m.typeMedia as typeMedia, m.illustreImgMedia as illustreImgMedia,a.statutArticle as statutArticle,
                                            a.archiveArticle as archiveArticle, a.corbeilleArticle as corbeilleArticle
                                          FROM utbAdminBundle:Article a INNER JOIN a.medias m INNER JOIN a.rubrique r
                                          WHERE  r.id =:type and a.id <>:id AND a.archiveArticle=0 AND a.corbeilleArticle=0 
                                          and trim(a.titreArticle) !=:vide
                                          ORDER BY a.ordre,a.id DESC');

        $query->setParameters(array('type' => $type, 'id' => $id, 'vide' => ''));
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
     *  Methode pour la liste des medias (documents, images ) lie a une article
     * 
     * Table(s):  Rubrique, Article
     * 
     * @param <string>  $locale : Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id :   Identifiant de l'article
     * @param <integer> $condition : 1- Pour une image | 2- Pour un document
     * 
     * @return <string> return le  resultat d'une requete 
     * 
     */
    public function getListeMedia($id, $condition, $locale) {

        $query = $this->_em->createQuery('SELECT a.id as id, m.id as idMedia, m.urlMedia as urlMedia, m.nomMedia as nomMedia
                                          FROM utbAdminBundle:Article a INNER JOIN a.medias m WHERE a.id = :id and  m.illustreImgMedia = 0 
                                          AND  m.typeMedia =:cond ORDER BY a.ordre,a.id DESC');
        $query->setParameters(array('id' => $id, 'cond' => $condition));
        $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        // Force the locale
        $query->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale
        );

        //var_dump($query->getResult());
        return $query->getResult();
    }
    
    /**
     *  Methode pour la liste des medias (documents, images ) lie a une article
     * 
     * Table(s):  Rubrique, Article
     * 
     * @param <string>  $locale : Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id :   Identifiant de l'article
     * @param <integer> $condition : 1- Pour une image | 2- Pour un document
     * 
     * @return <string> return le  resultat d'une requete 
     * 
     */
    public function getListeMediaIllust($id, $condition, $locale) {

        $query = $this->_em->createQuery('SELECT a.id as id, m.id as idMedia, m.urlMedia as urlMedia, m.nomMedia as nomMedia
                                          FROM utbAdminBundle:Article a INNER JOIN a.medias m WHERE m.id = :id and  m.illustreImgMedia = 1 
                                          AND  m.typeMedia =:cond ORDER BY a.ordre,a.id DESC');
        $query->setParameters(array('id' => $id, 'cond' => $condition));
        $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        // Force the locale
        $query->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale
        );

        //var_dump($query->getResult());
        return $query->getResult();
    }

    /**
     *  Methode pour avoir l'image illustrative d'une article
     * 
     * Table(s):  Rubrique, Article
     * 
     * @param <string>  $locale : Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id :   Identifiant de l'article
     * @param <integer> $condition : 1- Pour une image | 2- Pour un document
     * 
     * @return <string> return le  resultat d'une requete 
     * 
     */
    public function getImageUllistreMedia($id, $condition, $locale) {

        $query = $this->_em->createQuery('SELECT a.id as id, m.id as idMedia, m.urlMedia as urlMedia  FROM utbAdminBundle:Article a INNER JOIN a.medias m
            WHERE a.id = :id and  m.typeMedia =:cond AND a.archiveArticle!=1 AND a.corbeilleArticle!=1 ORDER BY a.ordre,a.id DESC');
        $query->setParameters(array('id' => $id, 'cond' => $condition));

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
     *  Methode pour avoir la liste des articles sur la page listeArticle.html.twig 
     * 
     * Table(s):  Rubrique, Article
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * @param <integer>  $page :  Variable intervenant dans la pagination
     * @param <integer> $total :  Nombre total d'article
     * @param <integer> $articles_per_page :  Nombre d'article par page
     * 
     * @return <string> return le  resultat d'une requete
     * 
     */
    public function findAllByLocale($locale, $total, $page, $articles_per_page) {
        //$articles_per_page = $this->container->getParameter('max_articles_on_listepage');
        //Make a Select query
        $query = $this->_em->createQuery('SELECT r.id as idrubrique,r.nomRubrique as nomRubrique,
            a.id as id, a.titreArticle as titreArticle, a.statutArticle as statutArticle,
            a.descriptionArticle as descriptionArticle,a.introTexteArticle,a.articleAjoutPar as articleAjoutPar,
            a.articleDateAjout as articleDateAjout, m.urlMedia as urlMedia,m.typeMedia as typeMedia, a.typePre as typePre
            FROM utbAdminBundle:Article a INNER JOIN a.rubrique r  INNER JOIN a.medias m
            WHERE m.illustreImgMedia=1 AND a.archiveArticle!=1 AND
            trim(a.titreArticle) !=:vide AND a.corbeilleArticle!=1 ORDER BY a.ordre,id DESC');

        $query->setParameter('vide', '');
        $query->setFirstResult(($page * $articles_per_page) - $articles_per_page);
        $query->setMaxResults($articles_per_page);

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
     *  Methode pour avoir la liste des articles sur la page listeArticle.html.twig 
     * 
     * Table(s):  Rubrique, Article
     * 
     * @author Edem
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * @param <integer>  $page :  Variable intervenant dans la pagination
     * @param <integer> $total :  Nombre total d'article
     * @param <integer> $articles_per_page :  Nombre d'article par page
     * 
     * @return <string> return le  resultat d'une requete
     * 
     */
    public function findAllRecentByLocale($locale = 'en', $total, $page, $articles_per_page) {
        //$articles_per_page = $this->container->getParameter('max_articles_on_listepage');
        //Make a Select query
        $query = $this->_em->createQuery('SELECT r.id as idrubrique,r.nomRubrique as nomRubrique,
            a.id as id, a.titreArticle as titreArticle, a.statutArticle as statutArticle,
            a.descriptionArticle as descriptionArticle,a.introTexteArticle,a.articleAjoutPar as articleAjoutPar,
            a.articleDateAjout as articleDateAjout, m.urlMedia as urlMedia,m.typeMedia as typeMedia
            FROM utbAdminBundle:Article a INNER JOIN a.rubrique r  INNER JOIN a.medias m
            WHERE m.illustreImgMedia=1 AND a.archiveArticle!=1 AND a.statutArticle=4 AND a.corbeilleArticle!=1 ORDER BY a.ordre,id DESC');

        $query->setFirstResult(($page * $articles_per_page) - $articles_per_page);
        $query->setMaxResults($articles_per_page);

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
     *  Methode pour avoir le nombre d'article dans la corbeille
     * 
     * Table(s):  Rubrique, Article
     * 
     * @author Edem
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le  count du resultat d'une requete
     * 
     */
    public function getTotalListeCorbeilleLocale($locale) {
        //Make a Select query
        $query = $this->_em->createQuery('SELECT r.id as idrubrique,r.nomRubrique as nomRubrique,
            a.id as id, a.titreArticle as titreArticle, a.statutArticle as statutArticle,
            a.descriptionArticle as descriptionArticle,a.introTexteArticle,a.articleAjoutPar as articleAjoutPar,
            a.articleDateAjout as articleDateAjout, m.urlMedia as urlMedia,m.typeMedia as typeMedia
            FROM utbAdminBundle:Article a INNER JOIN a.rubrique r  INNER JOIN a.medias m
            WHERE m.illustreImgMedia=1 AND a.archiveArticle!=1 AND a.corbeilleArticle=1 
            AND trim(a.titreArticle) !=:vide
            ORDER BY a.ordre,a.id DESC');
        $query->setParameter('vide', '');
        $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        // Force the locale
        $query->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale
        );
        return count($query->getResult());
    }

    /**
     *  Methode pour avoir le nombre d'article dans la corbeille dans une rubrique
     * 
     * Table(s):  Rubrique, Article, Media
     * 
     * @author Edem
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * @param <integer> $type : id de la rubrique dans laquelle se trouve l'article.
     * 
     * @return <string> return le  count du resultat d'une requete
     * 
     */
    public function getTotalListeCorbeilleRubriqueLocale($type, $locale = 'en') {
        //Make a Select query
        $query = $this->_em->createQuery('SELECT r.id as idrubrique,r.nomRubrique as nomRubrique,
            a.id as id, a.titreArticle as titreArticle, a.statutArticle as statutArticle,
            a.descriptionArticle as descriptionArticle,a.introTexteArticle,a.articleAjoutPar as articleAjoutPar,
            a.articleDateAjout as articleDateAjout, m.urlMedia as urlMedia,m.typeMedia as typeMedia
            FROM utbAdminBundle:Article a INNER JOIN a.rubrique r  INNER JOIN a.medias m
            WHERE r.id =:type AND m.illustreImgMedia=1 AND a.archiveArticle!=1 AND a.corbeilleArticle=1 
            AND trim(a.titreArticle) !=:vide
            ORDER BY a.ordre,a.id DESC');
        $query->setParameters(array('type' => $type, 'vide' => ''));

        $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        // Force the locale
        $query->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale
        );
        return count($query->getResult());
    }

    /**
     *  Methode pour avoir le nombre d'article dans la Soumis dans une rubrique
     * 
     * Table(s):  Rubrique, Article, Media
     * 
     * @author Edem
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * @param <integer> $type : id de la rubrique dans laquelle se trouve l'article.
     * 
     * @return <string> return le  count du resultat d'une requete
     * 
     */
    public function getTotalListeSoumisRubriqueLocale($type, $locale = 'en') {
        //Make a Select query
        $query = $this->_em->createQuery('SELECT r.id as idrubrique,r.nomRubrique as nomRubrique,
            a.id as id, a.titreArticle as titreArticle, a.statutArticle as statutArticle,
            a.descriptionArticle as descriptionArticle,a.introTexteArticle,a.articleAjoutPar as articleAjoutPar,
            a.articleDateAjout as articleDateAjout, m.urlMedia as urlMedia,m.typeMedia as typeMedia
            FROM utbAdminBundle:Article a INNER JOIN a.rubrique r  INNER JOIN a.medias m
            WHERE r.id =:type AND m.illustreImgMedia=1 AND a.corbeilleArticle=0 AND a.archiveArticle=0 
            AND a.statutArticle=2 AND trim(a.titreArticle) !=:vide ORDER BY a.ordre,a.id DESC');
        $query->setParameters(array('type' => $type, 'vide' => ''));

        $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        // Force the locale
        $query->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale
        );
        return count($query->getResult());
    }

    /**
     *  Methode pour avoir le nombre d'article en Attente dans une rubrique
     * 
     * Table(s):  Rubrique, Article, Media
     * 
     * @author Edem
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * @param <integer> $type : id de la rubrique dans laquelle se trouve l'article.
     * 
     * @return <string> return le  count du resultat d'une requete
     * 
     */
    public function getTotalListeAttenteRubriqueLocale($type, $locale = 'en') {
        //Make a Select query
        $query = $this->_em->createQuery('SELECT r.id as idrubrique,r.nomRubrique as nomRubrique,
            a.id as id, a.titreArticle as titreArticle, a.statutArticle as statutArticle,
            a.descriptionArticle as descriptionArticle,a.introTexteArticle,a.articleAjoutPar as articleAjoutPar,
            a.articleDateAjout as articleDateAjout, m.urlMedia as urlMedia,m.typeMedia as typeMedia
            FROM utbAdminBundle:Article a INNER JOIN a.rubrique r  INNER JOIN a.medias m
            WHERE r.id =:type AND m.illustreImgMedia=1 AND a.corbeilleArticle=0 AND a.archiveArticle=0 AND 
            a.statutArticle=3 and trim(a.titreArticle) !=:vide ORDER BY a.ordre,a.id DESC');
        $query->setParameters(array('type' => $type, 'vide' => ''));

        $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        // Force the locale
        $query->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale
        );
        return count($query->getResult());
    }

    /**
     *  Methode pour avoir le nombre d'article dans la corbeille dans une rubrique
     * 
     * Table(s):  Rubrique, Article, Media
     * 
     * @author Edem
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * @param <integer> $type : id de la rubrique dans laquelle se trouve l'article.
     * 
     * @return <string> return le  count du resultat d'une requete
     * 
     */
    public function getTotalListeArchiveRubriqueLocale($type, $locale = 'en') {
        //Make a Select query
        $query = $this->_em->createQuery('SELECT r.id as idrubrique,r.nomRubrique as nomRubrique,
            a.id as id, a.titreArticle as titreArticle, a.statutArticle as statutArticle,
            a.descriptionArticle as descriptionArticle,a.introTexteArticle,a.articleAjoutPar as articleAjoutPar,
            a.articleDateAjout as articleDateAjout, m.urlMedia as urlMedia,m.typeMedia as typeMedia
            FROM utbAdminBundle:Article a INNER JOIN a.rubrique r  INNER JOIN a.medias m
            WHERE r.id =:type AND m.illustreImgMedia=1 AND a.corbeilleArticle!=1 AND a.archiveArticle=1 
            AND trim(a.titreArticle) !=:vide ');
        $query->setParameters(array('type' => $type, 'vide' => ''));

        $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        // Force the locale
        $query->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale
        );
        return count($query->getResult());
    }

    /**
     *  Methode pour avoir la liste des articles de la corbeille sur la page listeArticleCorbeille.html.twig 
     * 
     * Table(s):  Rubrique, Article, Media
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * @param <integer> $type :  Variable pour avoir l'identifiant de la rubrique  dans laquelle se trouve l'article
     * @param <integer> $page :  Variable intervenant dans la pagination
     * @param <integer> $total :  Nombre total d'article
     * @param <integer> $articles_per_page :  Nombre d'article par page
     * 
     * @return <string> return le  resultat d'une requete
     * 
     */
    public function findAllByLocaleCorbeille($locale = 'en', $total, $page, $articles_per_page) {
        //$articles_per_page = $this->container->getParameter('max_articles_on_listepage');
        //Make a Select for articles in garbage corbeilleArticle=1
        $query = $this->_em->createQuery('SELECT r.id as idrubrique,r.nomRubrique as nomRubrique,
            a.id as id, a.titreArticle as titreArticle, a.statutArticle as statutArticle,
            a.descriptionArticle as descriptionArticle,a.introTexteArticle,a.articleAjoutPar as articleAjoutPar,
            a.articleDateAjout as articleDateAjout, m.urlMedia as urlMedia,m.typeMedia as typeMedia
            FROM utbAdminBundle:Article a INNER JOIN a.rubrique r  INNER JOIN a.medias m
            WHERE m.illustreImgMedia=1 AND a.archiveArticle!=1 AND a.corbeilleArticle=1 
            AND trim(a.titreArticle) !=:vide
            ORDER BY a.ordre,a.id DESC');

        $query->setParameter('vide', '');
        $query->setFirstResult(($page * $articles_per_page) - $articles_per_page);
        $query->setMaxResults($articles_per_page);

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
     *  Methode pour avoir la liste des articles de la corbeille dans une rubrique sur la page oneRubriqueCorbeille.html.twig 
     * 
     * Table(s):  Rubrique, Article, Media
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * @param <integer> $type :  Variable pour avoir l'identifiant de la rubrique  dans laquelle se trouve l'article
     * @param <integer> $page :  Variable intervenant dans la pagination
     * @param <integer> $total :  Nombre total d'article
     * @param <integer> $articles_per_page :  Nombre d'article par page
     * 
     * @return <string> return le  resultat d'une requete
     * 
     */
    public function findAllArticleByLocaleCorbeille($type, $locale = 'en', $total, $page, $articles_per_page) {
        //$articles_per_page = $this->container->getParameter('max_articles_on_listepage');
        //Make a Select for articles in garbage corbeilleArticle=1
        $query = $this->_em->createQuery('SELECT r.id as idrubrique,r.nomRubrique as nomRubrique,
            a.id as id, a.titreArticle as titreArticle, a.statutArticle as statutArticle,
            a.descriptionArticle as descriptionArticle,a.introTexteArticle,a.articleAjoutPar as articleAjoutPar,
            a.articleDateAjout as articleDateAjout, m.urlMedia as urlMedia,m.typeMedia as typeMedia
            FROM utbAdminBundle:Article a INNER JOIN a.rubrique r  INNER JOIN a.medias m
            WHERE m.illustreImgMedia=1 AND a.archiveArticle!=1 AND a.corbeilleArticle=1 AND  r.id=:type
            AND trim(a.titreArticle) !=:vide
            ORDER BY a.ordre,a.id DESC');
        $query->setParameters(array('type' => $type, 'vide' => ''));
        $query->setFirstResult(($page * $articles_per_page) - $articles_per_page);
        $query->setMaxResults($articles_per_page);

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
     *  Methode pour avoir la liste des articles soumis pour validation  dans une rubrique sur la page oneRubriqueSoumis.html.twig 
     * 
     * Table(s):  Rubrique, Article, Media
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * @param <integer> $type :  Variable pour avoir l'identifiant de la rubrique  dans laquelle se trouve l'article
     * @param <integer> $page :  Variable intervenant dans la pagination
     * @param <integer> $total :  Nombre total d'article
     * @param <integer> $articles_per_page :  Nombre d'article par page
     * 
     * @return <string> return le  resultat d'une requete
     * 
     */
    public function findAllArticleByLocaleSoumis($type, $locale = 'en', $total, $page, $articles_per_page) {
        //$articles_per_page = $this->container->getParameter('max_articles_on_listepage');
        //Make a Select for articles in garbage corbeilleArticle=1
        $query = $this->_em->createQuery('SELECT r.id as idrubrique,r.nomRubrique as nomRubrique,
            a.id as id, a.titreArticle as titreArticle, a.statutArticle as statutArticle,
            a.descriptionArticle as descriptionArticle,a.introTexteArticle,a.articleAjoutPar as articleAjoutPar,
            a.articleDateAjout as articleDateAjout, m.urlMedia as urlMedia,m.typeMedia as typeMedia
            FROM utbAdminBundle:Article a INNER JOIN a.rubrique r  INNER JOIN a.medias m
            WHERE a.corbeilleArticle=0 AND a.archiveArticle=0 AND a.statutArticle=2 and trim(a.titreArticle) !=:vide
            AND r.id=:type ORDER BY a.ordre,a.id DESC');
        $query->setParameters(array('type' => $type, 'vide' => ''));
        $query->setFirstResult(($page * $articles_per_page) - $articles_per_page);
        $query->setMaxResults($articles_per_page);

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
     *  Methode pour avoir la liste des articles en attente de publication   dans une rubrique sur la page oneRubriqueAttente.html.twig 
     * 
     * Table(s):  Rubrique, Article, Media
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * @param <integer> $type :  Variable pour avoir l'identifiant de la rubrique  dans laquelle se trouve l'article
     * @param <integer> $page :  Variable intervenant dans la pagination
     * @param <integer> $total :  Nombre total d'article
     * @param <integer> $articles_per_page :  Nombre d'article par page
     * 
     * @return <string> return le  resultat d'une requete
     * 
     */
    public function findAllArticleByLocaleAttente($type, $locale = 'en', $total, $page, $articles_per_page) {
        //$articles_per_page = $this->container->getParameter('max_articles_on_listepage');
        //Make a Select for articles in garbage corbeilleArticle=1
        $query = $this->_em->createQuery('SELECT r.id as idrubrique,r.nomRubrique as nomRubrique,
            a.id as id, a.titreArticle as titreArticle, a.statutArticle as statutArticle,
            a.descriptionArticle as descriptionArticle,a.introTexteArticle,a.articleAjoutPar as articleAjoutPar,
            a.articleDateAjout as articleDateAjout, m.urlMedia as urlMedia,m.typeMedia as typeMedia
            FROM utbAdminBundle:Article a INNER JOIN a.rubrique r  INNER JOIN a.medias m 
            WHERE a.corbeilleArticle=0 AND a.archiveArticle=0 AND a.statutArticle=3 and trim(a.titreArticle) !=:vide 
            AND r.id=:type ORDER BY a.ordre,a.id DESC');
        $query->setParameters(array('type' => $type, 'vide' => ''));
        $query->setFirstResult(($page * $articles_per_page) - $articles_per_page);
        $query->setMaxResults($articles_per_page);

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
     *  Methode pour avoir le total des articles en attente de publication
     * 
     * Table(s):  Rubrique, Article, Media
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le  count du resultat d'une requete
     * 
     */
    public function getTotalAttenteLocale($locale = 'en') {
        //Make a Select query
        $query = $this->_em->createQuery('SELECT r.id as idrubrique,
            r.nomRubrique as nomRubrique, a.id as id, a.titreArticle as titreArticle,
            a.descriptionArticle as descriptionArticle,a.introTexteArticle,a.articleDateAjout as articleDateAjout,
            m.urlMedia as urlMedia FROM utbAdminBundle:Article a INNER JOIN a.rubrique r  INNER JOIN a.medias m
            WHERE a.corbeilleArticle=0 AND a.archiveArticle=0 AND a.statutArticle=3 and trim(a.titreArticle) !=:vide 
            ORDER BY a.ordre,a.id DESC');
        $query->setParameter('vide', '');
        $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        // Force the locale
        $query->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale
        );
        return count($query->getResult());
    }

    /**
     *  Methode pour avoir la liste des articles en attente de publication   listeArticleAttente.html.twig 
     * 
     * Table(s):  Rubrique, Article, Media
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * @param <integer> $page :  Variable intervenant dans la pagination
     * @param <integer> $total :  Nombre total d'article
     * @param <integer> $articles_per_page :  Nombre d'article par page
     * 
     * @return <string> return le  resultat d'une requete
     * 
     */
    public function findAllByLocaleAttente($locale = 'en', $total, $page, $articles_per_page) {
        //$articles_per_page = $this->container->getParameter('max_articles_on_listepage');
        //
        $query = $this->_em->createQuery('SELECT r.id as idrubrique,r.nomRubrique as nomRubrique,
            a.id as id, a.titreArticle as titreArticle, a.descriptionArticle as descriptionArticle,a.introTexteArticle,
            a.articleDateAjout as articleDateAjout,a.statutArticle as statutArticle,
            a.articleAjoutPar as articleAjoutPar,m.urlMedia as urlMedia
            FROM utbAdminBundle:Article a INNER JOIN a.rubrique r  INNER JOIN a.medias m
            WHERE a.corbeilleArticle=0 AND a.archiveArticle=0 AND a.statutArticle=3 and trim(a.titreArticle) !=:vide AND 
            m.illustreImgMedia=1 
            ORDER BY a.ordre,a.id DESC');

        $query->setParameter('vide', '');
        $query->setFirstResult(($page * $articles_per_page) - $articles_per_page);
        $query->setMaxResults($articles_per_page);

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
     *  Methode pour avoir la liste des articles en recement publies listeArticleAttente.html.twig 
     * 
     * Table(s):  Rubrique, Article, Media
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le  resultat d'une requete
     * 
     */
    public function findAllByLocaleRecent($locale = 'en') {
        //$articles_per_page = $this->container->getParameter('max_articles_on_listepage');
        //Make a Select query
        $query = $this->_em->createQuery('SELECT r.id as idrubrique,r.nomRubrique as nomRubrique, a.id as id,a.statutArticle as statutArticle,
                                          a.titreArticle as titreArticle,a.articleAjoutPar as articleAjoutPar, a.descriptionArticle as descriptionArticle,a.introTexteArticle,
                                          a.articleDateAjout as articleDateAjout, m.urlMedia as urlMedia,m.typeMedia as typeMedia
                                          FROM utbAdminBundle:Article a INNER JOIN a.rubrique r  INNER JOIN a.medias m 
                                          WHERE m.illustreImgMedia=1 AND a.statutArticle != 5 AND a.statutArticle = 4 
                                          and m.illustreImgMedia=1 and trim(a.titreArticle) !=:vide 
                                          ORDER BY a.ordre,a.id DESC ');

        $query->setParameter('vide', '');
        $query->setFirstResult(0);
        $query->setMaxResults(5);

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
     *  Methode pour avoir la liste des articles en recement publies listeArticleAttente.html.twig 
     * 
     * Table(s):  Rubrique, Article, Media
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le  resultat d'une requete
     * 
     */
    public function findAllByLocaleAttente5($locale = 'en') {

        $query = $this->_em->createQuery('SELECT r.id as idrubrique,r.nomRubrique as nomRubrique,
            a.id as id, a.titreArticle as titreArticle, a.descriptionArticle as descriptionArticle,
            a.statutArticle as statutArticle,a.introTexteArticle,
            a.articleDateAjout as articleDateAjout,a.articleAjoutPar as articleAjoutPar, m.urlMedia as urlMedia
            FROM utbAdminBundle:Article a INNER JOIN a.rubrique r  INNER JOIN a.medias m
            WHERE a.corbeilleArticle=0 AND a.archiveArticle=0 AND m.illustreImgMedia=1 
            and trim(a.titreArticle) !=:vide
            AND a.statutArticle=3 ORDER BY a.ordre,a.id DESC');
        $query->setParameter('vide', '');

        $query->setFirstResult(0);
        $query->setMaxResults(5);

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
     *  Methode pour avoir le total des articles sur le twig listeArticle.twig
     * 
     * Table(s):  Rubrique, Article, Media
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le  count du resultat d'une requete
     * 
     */
    public function getTotalArticleLocale($locale) {
        //Make a Select query
        $query = $this->_em->createQuery('SELECT r.id as idrubrique,r.nomRubrique as nomRubrique,
            a.id as id, a.titreArticle as titreArticle, a.statutArticle as statutArticle,a.introTexteArticle,
            a.descriptionArticle as descriptionArticle,a.articleAjoutPar as articleAjoutPar,
            a.articleDateAjout as articleDateAjout, m.urlMedia as urlMedia,m.typeMedia as typeMedia
            FROM utbAdminBundle:Article a INNER JOIN a.rubrique r  INNER JOIN a.medias m
            WHERE m.illustreImgMedia=1 AND a.archiveArticle!=1 AND a.corbeilleArticle!=1 
            and trim(a.titreArticle) !=:vide ORDER BY a.ordre,a.id DESC
             ');
        $query->setParameter('vide', '');
        $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        // Force the locale
        $query->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale
        );
        return count($query->getResult());
    }

    /**
     *  Methode pour avoir le total des articles archives sur le twig listeArticleArchive.twig
     * 
     * Table(s):  Rubrique, Article, Media
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * @param <integer> $page :  Variable intervenant dans la pagination
     * @param <integer> $total :  Nombre total d'article
     * @param <integer> $articles_per_page :  Nombre d'article par page
     * 
     * @return <string> return le  count du resultat d'une requete    
     * 
     */
    public function getTotalListeArchiveLocale($locale) {
        //Make a Select query
        $query = $this->_em->createQuery('SELECT r.id as idrubrique,r.nomRubrique as nomRubrique,
            a.id as id, a.titreArticle as titreArticle, a.statutArticle as statutArticle,a.introTexteArticle,
            a.descriptionArticle as descriptionArticle,a.articleAjoutPar as articleAjoutPar,
            a.articleDateAjout as articleDateAjout, m.urlMedia as urlMedia,m.typeMedia as typeMedia
            FROM utbAdminBundle:Article a INNER JOIN a.rubrique r  INNER JOIN a.medias m
            WHERE m.illustreImgMedia=1 AND a.archiveArticle=1 AND a.corbeilleArticle!=1 
            and trim(a.titreArticle) !=:vide ORDER BY a.ordre,a.id DESC');

        $query->setParameter('vide', '');
        $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        // Force the locale
        $query->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale
        );
        return count($query->getResult());
    }

    /**
     *  Methode pour avoir le total des articles archives sur le twig listeArticleArchive.twig
     * 
     * Table(s):  Rubrique, Article, Media
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * @param <integer> $page :  Variable intervenant dans la pagination
     * @param <integer> $total :  Nombre total d'article
     * @param <integer> $articles_per_page :  Nombre d'article par page
     * 
     * @return <string> return le  resultat d'une requete
     * 
     */
    public function findAllByLocaleArchive($locale = 'en', $total, $page, $articles_per_page) {
        //$articles_per_page = $this->container->getParameter('max_articles_on_listepage');
        //Make a Select query
        $query = $this->_em->createQuery('SELECT r.id as idrubrique,r.nomRubrique as nomRubrique,
            a.id as id, a.titreArticle as titreArticle, a.statutArticle as statutArticle,a.introTexteArticle,
            a.descriptionArticle as descriptionArticle,a.articleAjoutPar as articleAjoutPar,
            a.articleDateAjout as articleDateAjout, m.urlMedia as urlMedia,m.typeMedia as typeMedia
            FROM utbAdminBundle:Article a INNER JOIN a.rubrique r  INNER JOIN a.medias m
            WHERE m.illustreImgMedia=1 AND a.archiveArticle=1 AND a.corbeilleArticle!=1 
            and trim(a.titreArticle) !=:vide
            ORDER BY a.ordre,a.id DESC');

        $query->setParameter('vide', '');
        $query->setFirstResult(($page * $articles_per_page) - $articles_per_page);
        $query->setMaxResults($articles_per_page);

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
     *  Methode pour avoir la liste des articles archives sur le twig oneRubriqueArchive.twig
     * 
     * Table(s):  Rubrique, Article, Media
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * @param <integer> $page :  Variable intervenant dans la pagination
     * @param <integer> $total :  Nombre total d'article
     * @param <integer> $articles_per_page :  Nombre d'article par page
     * 
     * @return <string> return le  resultat d'une requete
     * 
     */
    public function findAllArticleByLocaleArchive($type, $locale = 'en', $total, $page, $articles_per_page) {
        //$articles_per_page = $this->container->getParameter('max_articles_on_listepage');
        //Make a Select query
        $query = $this->_em->createQuery('SELECT r.id as idrubrique,r.nomRubrique as nomRubrique,
            a.id as id, a.titreArticle as titreArticle, a.statutArticle as statutArticle,a.introTexteArticle,
            a.descriptionArticle as descriptionArticle,a.articleAjoutPar as articleAjoutPar,
            a.articleDateAjout as articleDateAjout, m.urlMedia as urlMedia,m.typeMedia as typeMedia
            FROM utbAdminBundle:Article a INNER JOIN a.rubrique r  INNER JOIN a.medias m
            WHERE m.illustreImgMedia=1 AND a.archiveArticle=1 AND a.corbeilleArticle!=1 and r.id=:type 
            and trim(a.titreArticle) !=:vide ORDER BY a.ordre,a.id DESC');

        $query->setParameters(array('type' => $type, 'vide' => ''));

        $query->setFirstResult(($page * $articles_per_page) - $articles_per_page);
        $query->setMaxResults($articles_per_page);

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
     *  Methode pour avoir le total des articles soumis pour validation sur la page listeArticleSoumis.html.twig
     * 
     * Table(s):  Rubrique, Article, Media
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le  count du resultat d'une requete
     * 
     */
    public function getTotalSoumisLocale($locale) {
        //Make a Select query
        $query = $this->_em->createQuery('SELECT r.id as idrubrique,
            r.nomRubrique as nomRubrique, a.id as id, a.titreArticle as titreArticle,
            a.descriptionArticle as descriptionArticle,a.articleDateAjout as articleDateAjout,a.introTexteArticle, 
            m.urlMedia as urlMedia FROM utbAdminBundle:Article a INNER JOIN a.rubrique r  INNER JOIN a.medias m
            WHERE a.corbeilleArticle=0 AND a.archiveArticle=0 AND a.statutArticle=2 
            AND m.illustreImgMedia = 1 and trim(a.titreArticle) !=:vide');

        $query->setParameter('vide', '');
        $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        // Force the locale
        $query->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale
        );
        return count($query->getResult());
    }

    /**
     *  Methode pour avoir la liste des articles soumis pour validation sur la page listeArticleSoumis.html.twig
     * 
     * Table(s):  Rubrique, Article, Media
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * @param <integer> $page :  Variable intervenant dans la pagination
     * @param <integer> $total :  Nombre total d'article
     * @param <integer> $articles_per_page :  Nombre d'article par page
     * 
     * @return <string> return le  resultat d'une requete
     * 
     */
    public function findAllByLocaleSoumis($locale, $total, $page, $articles_per_page) {
        //$articles_per_page = $this->container->getParameter('max_articles_on_listepage');
        //
        $query = $this->_em->createQuery('SELECT r.id as idrubrique,r.nomRubrique as nomRubrique,
            a.id as id, a.titreArticle as titreArticle, a.descriptionArticle as descriptionArticle,
            a.articleDateAjout as articleDateAjout,a.statutArticle as statutArticle,a.introTexteArticle,
            a.articleAjoutPar as articleAjoutPar,m.urlMedia as urlMedia
            FROM utbAdminBundle:Article a INNER JOIN a.rubrique r  INNER JOIN a.medias m
            WHERE a.corbeilleArticle=0 AND a.archiveArticle=0 AND m.illustreImgMedia = 1
            and trim(a.titreArticle) !=:vide 
            AND a.statutArticle=2 ORDER BY a.ordre,a.id DESC');

        $query->setParameter('vide', '');
        $query->setFirstResult(($page * $articles_per_page) - $articles_per_page);
        $query->setMaxResults($articles_per_page);

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
     *  Methode pour avoir le total des articles recement publies  sur la page listeArticleRecent.html.twig
     * 
     * Table(s):  Rubrique, Article, Media
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site
     * @param <integer> $page :  Variable intervenant dans la pagination
     * @param <integer> $total :  Nombre total d'article
     * @param <integer> $articles_per_page :  Nombre d'article par page
     * 
     * @return <string> return le  count du resultat d'une requete
     * 
     */
    public function getTotalArticleRecentLocale($locale) {
        //Make a Select query
        $query = $this->_em->createQuery('SELECT r.id as idrubrique,r.nomRubrique as nomRubrique,
            a.id as id, a.titreArticle as titreArticle, a.statutArticle as statutArticle,
            a.descriptionArticle as descriptionArticle,a.articleAjoutPar as articleAjoutPar,a.introTexteArticle,
            a.articleDateAjout as articleDateAjout, m.urlMedia as urlMedia,m.typeMedia as typeMedia
            FROM utbAdminBundle:Article a INNER JOIN a.rubrique r  INNER JOIN a.medias m
            WHERE m.illustreImgMedia=1 AND a.archiveArticle!=1 AND a.corbeilleArticle!=1 AND m.illustreImgMedia = 1
            AND a.statutArticle=4  AND trim(a.titreArticle) !=:vide ORDER BY a.ordre,a.id DESC
             ');
        $query->setParameter('vide', '');
        $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        // Force the locale
        $query->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale
        );
        return count($query->getResult());
    }

    public function findAllByLocaleType($locale, $type, $nbrerecord, $idrub) {

        $sql = 'SELECT r.id as idrubrique,r.nomRubrique as nomRubrique,
                       a.id as id, a.titreArticle as titreArticle, a.descriptionArticle as descriptionArticle,
                       a.statutArticle as statutArticle,a.introTexteArticle,
                       a.articleDateAjout as articleDateAjout,a.articleAjoutPar as articleAjoutPar, m.urlMedia as urlMedia, a.typePre as typePre
                FROM utbAdminBundle:Article a INNER JOIN a.rubrique r  INNER JOIN a.medias m
                WHERE a.corbeilleArticle=0 AND a.archiveArticle=0 AND m.illustreImgMedia = 1 
                AND trim(a.titreArticle) !=:vide AND a.statutArticle= :type ';

        $idrub == 0 ? $sql.='' : $sql.=' AND r.id = :idrubrique ';
        //$sql.= ' GROUP BY a.statutArticle ';
        $sql.= ' ORDER BY a.ordre,a.id DESC';
        $query = $this->_em->createQuery($sql);

        $idrub == 0 ? $query->setParameters(array('type' => $type, 'vide' => '')) : $query->setParameters(array('type' => $type, 'vide' => '', 'idrubrique' => $idrub));

        $query->setFirstResult(0);
        $query->setMaxResults($nbrerecord);

        $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        // Force the locale
        $query->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale
        );
        return $query->getResult();
    }

    public function findDeuxByLocaleType($locale, $type, $nbrerecord, $idrub) {
        $sql = 'SELECT r.id as idrubrique,r.nomRubrique as nomRubrique,
                       a.id as id, a.titreArticle as titreArticle, a.descriptionArticle as descriptionArticle,
                       a.statutArticle as statutArticle,a.introTexteArticle,
                       a.articleDateAjout as articleDateAjout,a.articleAjoutPar as articleAjoutPar, m.urlMedia as urlMedia
                FROM utbAdminBundle:Article a INNER JOIN a.rubrique r  INNER JOIN a.medias m
                WHERE a.corbeilleArticle=0 AND a.archiveArticle=0 AND m.illustreImgMedia = 1 
                AND trim(a.titreArticle) !=:vide AND a.statutArticle= :type ';

        $idrub == 0 ? $sql.='' : $sql.=' AND r.id = :idrubrique ';

        $sql.= 'ORDER BY a.ordre,a.id DESC ';
        $query = $this->_em->createQuery($sql);

        $idrub == 0 ? $query->setParameters(array('type' => $type, 'vide' => '')) : $query->setParameters(array('type' => $type, 'vide' => '', 'idrubrique' => $idrub));

        $query->setFirstResult(0);
        $query->setMaxResults(2);

        $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        // Force the locale
        $query->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale
        );
        return $query->getResult();
    }

    public function afficherActuAccueil($locale, $type, $nbrerecord, $idrub) {

        $sql = 'SELECT r.id as idrubrique,r.nomRubrique as nomRubrique,p.id as idparent,
                       a.id as id, a.titreArticle as titreArticle, a.descriptionArticle as descriptionArticle,
                       a.statutArticle as statutArticle,a.introTexteArticle,
                       a.articleDateAjout as articleDateAjout,a.articleAjoutPar as articleAjoutPar, m.urlMedia as urlMedia                      
                FROM utbAdminBundle:Article a INNER JOIN a.rubrique r INNER JOIN r.idparent p INNER JOIN a.medias m
                WHERE a.corbeilleArticle=0 AND a.archiveArticle=0 AND m.illustreImgMedia = 1 
                AND trim(a.titreArticle) !=:vide AND a.statutArticle= :type ';

        $idrub == 0 ? $sql.='' : $sql.=' AND r.id=:idrubrique';
        $sql.= ' ORDER BY a.ordre,a.id DESC';
        $query = $this->_em->createQuery($sql);

        $idrub == 0 ? $query->setParameters(array('type' => $type, 'vide' => '')) : $query->setParameters(array('type' => $type, 'vide' => '', 'idrubrique' => $idrub));

        $query->setFirstResult(0);
        $query->setMaxResults($nbrerecord);

        $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        // Force the locale
        $query->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale
        );
        return $query->getResult();
    }

    
    public function afficherBreveAccueil($locale, $type, $nbrerecord, $idrub) {

        $sql = 'SELECT r.id as idrubrique,r.nomRubrique as nomRubrique,p.id as idparent,
                       a.id as id, a.titreArticle as titreArticle, a.descriptionArticle as descriptionArticle,
                       a.statutArticle as statutArticle,a.introTexteArticle,
                       a.articleDateAjout as articleDateAjout,a.articleAjoutPar as articleAjoutPar, m.urlMedia as urlMedia
                FROM utbAdminBundle:Article a INNER JOIN a.rubrique r INNER JOIN r.idparent p INNER JOIN a.medias m
                WHERE a.corbeilleArticle=0 AND a.archiveArticle=0 AND m.illustreImgMedia = 1 
                AND trim(a.titreArticle) !=:vide AND a.statutArticle= :type ';

        $idrub == 0 ? $sql.='' : $sql.=' AND r.id=:idrubrique';
        //$sql.= ' GROUP BY r.id ';
        $sql.= ' ORDER BY a.ordre,a.id DESC';
        $query = $this->_em->createQuery($sql);

        $idrub == 0 ? $query->setParameters(array('type' => $type, 'vide' => '')) : $query->setParameters(array('type' => $type, 'vide' => '', 'idrubrique' => $idrub));

        $query->setFirstResult(0);
        $query->setMaxResults($nbrerecord);

        $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        // Force the locale
        $query->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale
        );
        return $query->getResult();
    }

    public function findAllByLocalePublication($locale, $type, $nbrerecord, $idrub, $date, $typecate) {

        $param = array();
        $sql = 'SELECT r.id as idrubrique,r.nomRubrique as nomRubrique,
                       a.id as id, a.titreArticle as titreArticle, a.descriptionArticle as descriptionArticle,a.introTexteArticle,
                       a.statutArticle as statutArticle,n.libNatureDoc as libNatureDoc,
                       a.articleDateAjout as articleDateAjout,a.articleAjoutPar as articleAjoutPar, m.urlMedia as urlMedia
                FROM utbAdminBundle:Article a INNER JOIN a.rubrique r  INNER JOIN a.medias m
               INNER JOIN m.natureDoc n WHERE a.corbeilleArticle=0 AND a.archiveArticle=0 AND m.illustreImgMedia = 1 
                AND trim(a.titreArticle) !=:vide AND a.statutArticle= :type ';

        if ($idrub == 0) {
            $sql.='';
        } else {
            $sql.=' AND r.id = :idrubrique ';
            $param['idrubrique'] = $idrub;
        }
        if ($date == 0) {
            $sql.='';
        } else {
            $sql.=' AND r.articleDateAjout = :dateaj';
            $param['dateaj'] = $date;
        }
        if ($typecate == 0) {
            $sql.='';
        } else {
            $sql.=' AND n.id = :typecate ';
            $param['typecate'] = $typecate;
        }


        $param['vide'] = "";
        $param['type'] = $type;
        $sql.= 'ORDER BY a.ordre,a.id DESC';
        $query = $this->_em->createQuery($sql);

        $query->setParameters($param);

        $query->setFirstResult(0);
        $query->setMaxResults($nbrerecord);

        $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        // Force the locale
        $query->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale
        );
        return $query->getResult();
    }

    public function findAllByStatutLocale($statut, $locale, $page, $articles_per_page, $rubrique) {

        if ($statut == 0) {
            $statuttemp = '1,2,3,5,6';
        } else {
            $statuttemp = $statut;
        }

        $sql = '';
        $sql = 'SELECT r.id as idrubrique,r.nomRubrique as nomRubrique,
                    a.id as id, a.titreArticle as titreArticle, a.descriptionArticle as descriptionArticle,
                    a.articleDateAjout as articleDateAjout,a.statutArticle as statutArticle,a.introTexteArticle,
                    a.articleAjoutPar as articleAjoutPar,m.urlMedia as urlMedia
            FROM utbAdminBundle:Article a INNER JOIN a.rubrique r INNER JOIN a.medias m
            WHERE a.corbeilleArticle=0 AND a.archiveArticle=0 
            AND trim(a.titreArticle) !=:vide AND m.illustreImgMedia = 1 ';


        ($rubrique != 0) ? $sql .= ' AND r.id =:rubrik' : $sql .= '';

        $sql .= ' AND a.statutArticle IN (' . $statuttemp . ') ';

        $sql .=' ORDER BY a.ordre,a.id DESC ';

        $query = $this->_em->createQuery($sql);

        ($rubrique != 0) ?
                        $query->setParameters(array('vide' => '', 'rubrik' => $rubrique)) :
                        $query->setParameters(array('vide' => ''));



        $query->setFirstResult(($page * $articles_per_page) - $articles_per_page);
        $query->setMaxResults($articles_per_page);

        $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        // Force the locale
        $query->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale
        );
        return $query->getResult();
    }

    public function findAllByStatutLimitDesc($statut, $locale, $page, $articles_per_page, $rubrique) {

        if ($statut == 0) {
            $statuttemp = '1,2,3,5';
        } else {
            $statuttemp = $statut;
        }

        //substring(trim(a.descriptionArticle),1,200)

        $sql = ''; //                    
        $sql = 'SELECT r.id as idrubrique,r.nomRubrique as nomRubrique,
                    a.id as id, a.titreArticle as titreArticle, a.descriptionArticle as descriptionArticle,
                    a.articleDateAjout as articleDateAjout,a.statutArticle as statutArticle,a.introTexteArticle,
                    a.articleAjoutPar as articleAjoutPar,m.urlMedia as urlMedia
            FROM utbAdminBundle:Article a INNER JOIN a.rubrique r INNER JOIN a.medias m
            WHERE a.corbeilleArticle=0 AND a.archiveArticle=0 
            AND trim(a.titreArticle) !=:vide AND m.illustreImgMedia = 1 ';


        ($rubrique != 0) ? $sql .= ' AND r.id =:rubrik' : $sql .= '';

        $sql .= ' AND a.statutArticle IN (' . $statuttemp . ') ';

        $sql .=' ORDER BY a.ordre,a.id DESC ';

        $query = $this->_em->createQuery($sql);

        ($rubrique != 0) ?
                        $query->setParameters(array('vide' => '', 'rubrik' => $rubrique)) :
                        $query->setParameters(array('vide' => ''));



        $query->setFirstResult(($page * $articles_per_page) - $articles_per_page);
        $query->setMaxResults($articles_per_page);

        $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        // Force the locale
        $query->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale
        );
        return $query->getResult();
    }

    public function getTotalArticleParStatut($locale, $statut, $rubrique) {

        if ($statut == 0) {
            $statuttemp = '1,2,3,5,6';
        } else {
            $statuttemp = $statut;
        }

        $sql = '';
        $sql = 'SELECT count(r.id)  
              FROM utbAdminBundle:Article a INNER JOIN a.rubrique r  INNER JOIN a.medias m
              WHERE m.illustreImgMedia=1 AND a.archiveArticle!=1 AND a.corbeilleArticle!=1 
              AND trim(a.titreArticle) !=:vide  ';



        ($rubrique != 0) ? $sql .=' AND r.id =:rubrik' : $sql .= '';

        $sql .= ' AND a.statutArticle IN (' . $statuttemp . ') ';

        $query = $this->_em->createQuery($sql);

        ($rubrique != 0) ?
                        $query->setParameters(array('vide' => '', 'rubrik' => $rubrique)) :
                        $query->setParameters(array('vide' => ''));

        $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );

        // Force the locale
        $query->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale
        );

        try {
            $lavaleur = $query->getSingleScalarResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            $lavaleur = 0;
        }

        return $lavaleur;
    }

    public function getTestNomArticle($nomarticle, $idrubrique) {

        $query = $this->_em->createQuery('SELECT count(r.id)  
              FROM utbAdminBundle:Article a INNER JOIN a.rubrique r  Where  
              a.titreArticle=:nomarticle AND r.id=:idrubrique');
        $query->setParameters(array('nomarticle' => $nomarticle, 'idrubrique' => $idrubrique));
        try {
            $lavaleur = $query->getSingleScalarResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            $lavaleur = 0;
        }
        return $lavaleur;
    }

    public function getSiTraductionArtExiste($idrubrique, $locale, $entetetraduit = array(), $type, $page, $articles_per_page) {

        $sql1 = '';
        $resultat = array();


        if ($type == 0) {  //articles
            $sql1 = 'SELECT a.id, a.titreArticle , a.statutArticle,r.nomRubrique, r.id as numrub
                    FROM   utbAdminBundle:Article a INNER JOIN a.rubrique r 
                    WHERE  (trim(a.titreArticle) != :vide) ';

            ($idrubrique == 0) ? $sql1 .='' : $sql1.=' AND  r.id = :id ';
        } elseif ($type == 1) { //rubriques
            $sql1 = 'SELECT r.id , r.nomRubrique as  titreArticle , r.idgrandparent as statutArticle , p.nomRubrique ,  p.id as numrub  
                  FROM   utbAdminBundle:Rubrique r INNER JOIN r.idparent p  
                  WHERE  trim(r.nomRubrique) != :vide  ';

            ($idrubrique == 0) ? $sql1 .='' : $sql1.=' AND p.id = :id  or r.idgrandparent = :id ';
        } elseif ($type == 2) { //menu
            $sql1 = 'SELECT count(m.id) 
                  FROM   utbAdminBundle:Menu m
                  WHERE  trim(m.libMenu) != :vide  ';

            ($idrubrique == 0) ? $sql1 .='' : $sql1.=' AND  m.idParentMenu = :id group by m.id ';
        }

        $q1 = null;
        $q1 = $this->_em->createQuery($sql1);
        $q1->setFirstResult(($page * $articles_per_page) - $articles_per_page);
        $q1->setMaxResults($articles_per_page);
        
        if ($idrubrique != 0) {
            $q1->setParameters(array('id' => $idrubrique, 'vide' => ''));
        } else {
            $q1->setParameters(array('vide' => ''));
        }

        $q1->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker');
        // Force the locale
        $q1->setHint(\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale);


        $listearticle = $q1->getResult();

        //var_dump($listearticle);

        $nbretradparlangue = 0;

        if ($listearticle != null) {

            foreach ($listearticle as $keyarticle => $article) {

                $sql2 = '';
                $languepararticle = array();
                $sql2 = 'SELECT l.codeLangue , l.libLangue
                        FROM   utbAdminBundle:Langue l
                        WHERE  l.langueEtat =1 AND l.codeLangue != :code';

                $q2 = $this->_em->createQuery($sql2);
                $q2->setParameters(array('code' => $locale));
                
                $q2->setHint(
                        \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker');
                // Force the locale
                $q2->setHint(\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale);

                try {
                    $listelangue = $q2->getResult();
                } catch (\Doctrine\ORM\NoResultException $e) {
                    $listelangue = null;
                }

                $resultat[$article['id']] =
                        array(
                            'N°' => $article['id'],
                            $entetetraduit[0] => $article['titreArticle'],
                            /* $entetetraduit[1] */
                            'Stat.' => $article['statutArticle'],
                            $entetetraduit[2] => $article['nomRubrique'],
                            'NRub' => $article['numrub']
                );

                if ($listelangue != null) {

                    foreach ($listelangue as $cle => $langue) {
                        $sql3 = '';

                        if ($type == 0) { //articles
                            $sql3 = 'SELECT count(a.id)
								FROM   utbAdminBundle:Article a INNER JOIN a.rubrique r 
								WHERE  trim(a.titreArticle) != :vide and a.id = :idarticle ';
                        } elseif ($type == 1) { //rubriques
                            $sql3 = 'SELECT count(r.id)
                                                              FROM   utbAdminBundle:Rubrique r INNER JOIN r.idparent p  
                                                              WHERE  trim(r.nomRubrique) != :vide  AND r.id = :idarticle ';
                        } elseif ($type == 2) { //menu
                            $sql3 = 'SELECT count(m.id) 
                                                              FROM   utbAdminBundle:Menu m
                                                              WHERE  trim(m.libMenu) != :vide AND m.id = :idarticle ';
                        }


                        $q3 = $this->_em->createQuery($sql3);
                        /*$q3->setFirstResult(($page * $articles_per_page) - $articles_per_page);
                        $q3->setMaxResults($articles_per_page);*/
                        $q3->setParameters(array('vide' => '', 'idarticle' => $article['id']));

                        $q3->setHint(
                                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker');
                        // Force the locale
                        $q3->setHint(\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $langue['codeLangue']);

                        try {
                            $lecount = $q3->getSingleScalarResult();
                        } catch (\Doctrine\ORM\NoResultException $e) {
                            $lecount = 0;
                        }
                        //$resultat[$article['id']]['titre '.$langue['libLangue']] = 
                        $resultat[$article['id']][$article['id'] . '|' . $langue['codeLangue'] . '|' . $langue['libLangue']] = $lecount;

                        $nbretradparlangue .= 1;
                    }
                }
            }
        }
        return $resultat;
    }

    public function getTauxTraduction($idrub, $locale, $type) {

        /*         * ** liste des idarticles recuperes dans la langue locale passee *** */
        $resultat = array();
        if ($type == 0) {  //traduction des articles 
            $sqlliste = 'SELECT a.id
                          FROM   utbAdminBundle:Article a INNER JOIN a.rubrique r  
                          WHERE  (trim(a.titreArticle) != :vide) ';
            ($idrub == 0) ? $sqlliste .='' : $sqlliste.=' AND  r.id = :id ';
        } elseif ($type == 1) { //traduction des rubriques
            $sqlliste = 'SELECT r.id 
                          FROM   utbAdminBundle:Rubrique r INNER JOIN r.idparent p  
                          WHERE  trim(r.nomRubrique) != :vide  ';

            ($idrub == 0) ? $sqlliste .='' : $sqlliste.=' AND p.id = :id  ';
        } elseif ($type == 2) { //traduction des menus
            $sqlliste = 'SELECT m.id 
                          FROM   utbAdminBundle:Menu m
                          WHERE  trim(a.libMenu) != :vide  ';

            ($idrub == 0) ? $sqlliste .='' : $sqlliste.=' AND  m.idParentMenu = :id group by m.id ';
        }

        $qliste = null;
        $qliste = $this->_em->createQuery($sqlliste);

        if ($idrub != 0) {
            $qliste->setParameters(array('id' => $idrub, 'vide' => ''));
        } else {
            $qliste->setParameters(array('vide' => ''));
        }

        $qliste->setHint(\Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker');
        // Force the locale
        $qliste->setHint(\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale);

        try {
            $listelocale = $qliste->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            $listelocale = null;
        }
        //var_dump($listelocale);
        /*         * ******** Fin recup liste total ********* */

        // initilaisation du nombre de traduction
        $nbretraduction = 0;

        /*         * ******** Recup liste des langues actives autre que celle passee ********* */
        $sql2 = '';
        $sql2 = 'SELECT l.codeLangue , l.libLangue
                    FROM   utbAdminBundle:Langue l
                    WHERE  l.langueEtat =1 and l.codeLangue != :code  ';
        $q2 = $this->_em->createQuery($sql2);
        $q2->setParameters(array('code' => $locale));


        $q2->setHint(\Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker');
        // Force the locale
        $q2->setHint(\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale);


        try {
            $listelangue = $q2->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            $listelangue = null;
        }

        $nbretotal = null;
        $nbretotal = count($listelocale);

        if ($listelangue != null) {

            foreach ($listelangue as $cle => $langue) {

                $nbretraduction = 0;

                if ($listelocale != null) {
                    foreach ($listelocale as $articleid) {

                        $sql3 = '';

                        if ($type == 0) { //traduction des articles
                            $sql3 = 'SELECT count(a.id)
                                            FROM   utbAdminBundle:Article a INNER JOIN a.rubrique r 
                                            WHERE  trim(a.titreArticle) != :vide and a.id = :idArticle  ';
                        } elseif ($type == 1) { //traduction des rubriques
                            $sql3 = 'SELECT count(r.id) 
                                                  FROM   utbAdminBundle:Rubrique r INNER JOIN r.idparent p  
                                                  WHERE  trim(r.nomRubrique) != :vide and r.id = :idArticle  ';
                        } elseif ($type == 2) { //traduction des menus
                            $sql3 = 'SELECT count(m.id) 
                                                  FROM   utbAdminBundle:Menu m
                                                  WHERE  trim(a.libMenu) != :vide and m.id = :idArticle ';
                        }

                        $q3 = $this->_em->createQuery($sql3);
                        $q3->setParameters(array('vide' => '', 'idArticle' => $articleid));

                        $q3->setHint(
                                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker');
                        // Force the locale
                        $q3->setHint(\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $langue['codeLangue']);

                        try {
                            $lecount = $q3->getSingleScalarResult();
                        } catch (\Doctrine\ORM\NoResultException $e) {
                            $lecount = 0;
                        }

                        $nbretraduction +=$lecount;
                    }
                    $resultat[] = array(
                        //'Total' => $nbretotal,
                        'Lg' => $langue['libLangue'],
                        'prcent' => round(($nbretraduction * 100) / $nbretotal) . ' %' . ' - ' . '(' . $nbretraduction . '/' . $nbretotal . ')',
                        'total'=>$nbretotal
                    );
                }
            }
        }
        //}   

        return $resultat;
    }

    public function testSiArticle($id, $locale, $type) {

        $sql = '';
        //,a.titreArticle
        if ($type == 0) {

            $sql = 'SELECT count(a.id)
                   FROM   utbAdminBundle:Article a INNER JOIN a.rubrique r 
                   WHERE  trim(a.titreArticle) != :vide and a.id = :id ';
        } elseif ($type == 1) {

            $sql = 'SELECT count(r.id)
                   FROM   utbAdminBundle:Rubrique r
                   WHERE  trim(r.nomRubrique) != :vide and r.id = :id ';
        } /* elseif ($type == 2){

          $sql ='SELECT count(a.id)
          FROM   utbAdminBundle:Article a INNER JOIN a.rubrique r
          WHERE  trim(a.titreArticle) != :vide and a.id = :idarticle ';

          } elseif ($type == 3){

          $sql ='SELECT count(a.id)
          FROM   utbAdminBundle:Article a INNER JOIN a.rubrique r
          WHERE  trim(a.titreArticle) != :vide and a.id = :idarticle ';

          } elseif ($type == 4){

          $sql ='SELECT count(a.id)
          FROM   utbAdminBundle:Article a INNER JOIN a.rubrique r
          WHERE  trim(a.titreArticle) != :vide and a.id = :idarticle ';

          } */

        $q = $this->_em->createQuery($sql);
        $q->setParameters(array('vide' => '', 'id' => $id));

        $q->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker');
        // Force the locale
        $q->setHint(\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale);

        try {
            $lecount = $q->getSingleScalarResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            $lecount = 0;
        }
    }

    public function findAllByLocaleArbre($locale) {


        //Make a Select query
        $query = $this->_em->createQuery('SELECT r.id as idrubrique,r.nomRubrique as nomRubrique,
            a.id as id, a.titreArticle as titreArticle, a.statutArticle as statutArticle,
            a.descriptionArticle as descriptionArticle,a.articleAjoutPar as articleAjoutPar,
            a.articleDateAjout as articleDateAjout, m.urlMedia as urlMedia,m.typeMedia as typeMedia        
            FROM utbAdminBundle:Article a INNER JOIN a.rubrique r  INNER JOIN a.medias m
            WHERE m.illustreImgMedia=1 ORDER BY a.ordre,id DESC');

        $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        // Force the locale
        $query->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale
        );
        return $query->getResult();
    }

    public function getArticlesByLocaleRecherche($motcle, $locale) {
        //requête de recherche
        $query = $this->_em->createQuery('SELECT a  FROM utbAdminBundle:Article 
                                          WHERE  (a.id like :motcle) or (a.titre like :motcle) or
                                          (a.introTexteArticle like :motcle) or (titreArticle like :motcle)
                                         ');
        $query->setParameter('motcle', '%' . $motcle . '%');

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
     *  Methode pour la liste des cadres lie a un article
     * 
     * Table(s):  Cadre, Article
     * 
     * @param <string>  $locale : Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id :   Identifiant de l'article
     * 
     * @return <string> return le  resultat d'une requete 
     * 
     */
    public function getListeCadre($id, $locale) {

        $query = $this->_em->createQuery('SELECT a.id as idarticle, c.id as id, c.libCadre as libCadre,
                                          c.contenuCadre as contenuCadre,c.etatCadre as etat
                                          t.id as idtype, t.libTypeCadre as libtype
                                          FROM utbAdminBundle:Article a INNER JOIN a.cadres c INNER JOIN c.typeCadre t
                                          WHERE a.id = :id
                                          ORDER BY a.id DESC');
        $query->setParameters(array('id' => $id));

        //var_dump($query->getResult());
        return $query->getResult();
    }

    /**
     *  Methode pour la liste des medias (documents, images ) lie a une article
     * 
     * Table(s):  Rubrique, Article
     * 
     * @param <string>  $locale : Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id :   Identifiant de l'article
     * 
     * @return <string> return le  resultat d'une requete 
     * 
     */
    public function getListeCadreAbsent($ids, $locale) {
        $tab = explode(",", $ids);
        $query = $this->_em->createQuery('SELECT c.id as id, c.libCadre as libCadre,
                                          c.contenuCadre as contenuCadre,c.etatCadre as etat,
                                          t.id as idtype, t.libTypeCadre as libtype
                                          FROM utbAdminBundle:Cadre c INNER JOIN c.typeCadre t                                           
                                          WHERE c.id not IN (:ids) ORDER BY c.id DESC');
        $query->setParameter('ids', $tab);

        //var_dump($query->getResult());
        return $query->getResult();
    }

    public function findAllCadreByArticleLocale($locale) {
        //Make a Select query 
        $query = $this->_em->createQuery('SELECT c.id as id, c.libCadre as libCadre,
                                         c.contenuCadre as contenuCadre,c.etatCadre as etat,
                                         t.id as idtype, t.libTypeCadre as libtype
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

}
