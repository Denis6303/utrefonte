<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250510194825 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE abonne (idabonne INT AUTO_INCREMENT NOT NULL, idprofil INT DEFAULT NULL, idabonneparent INT DEFAULT NULL, mailinternaute VARCHAR(180) DEFAULT NULL, username VARCHAR(50) NOT NULL, nomprenom VARCHAR(100) NOT NULL, email VARCHAR(180) NOT NULL, telabonne VARCHAR(16) DEFAULT NULL, adresseabonne LONGTEXT NOT NULL, celabonne VARCHAR(16) NOT NULL, genpsswd VARCHAR(16) DEFAULT NULL, etatabonne INT NOT NULL, attempt INT NOT NULL, suppr INT NOT NULL, radicalabonne VARCHAR(8) DEFAULT NULL, salt VARCHAR(32) DEFAULT NULL, password VARCHAR(255) NOT NULL, controlecode INT DEFAULT NULL, codebase VARCHAR(16) DEFAULT NULL, codeop VARCHAR(16) DEFAULT NULL, compteparents LONGTEXT DEFAULT NULL, dateinscription DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', UNIQUE INDEX UNIQ_76328BF0F85E0677 (username), UNIQUE INDEX UNIQ_76328BF0E7927C74 (email), INDEX IDX_76328BF0826B1F3B (idprofil), INDEX IDX_76328BF0A2DBA314 (idabonneparent), INDEX IDX_76328BF059C828B2 (mailinternaute), PRIMARY KEY(idabonne)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE action (idaction INT AUTO_INCREMENT NOT NULL, libelleaction VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, datecreation DATETIME NOT NULL, dateexpiration DATETIME DEFAULT NULL, etat TINYINT(1) NOT NULL, PRIMARY KEY(idaction)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE adresse (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, rue VARCHAR(255) NOT NULL, ville VARCHAR(255) NOT NULL, code_postal VARCHAR(255) NOT NULL, pays VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_C35F0816A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE adresseip (idip INT AUTO_INCREMENT NOT NULL, idopinion INT NOT NULL, ip VARCHAR(45) NOT NULL, INDEX IDX_B7A04D977406E249 (idopinion), PRIMARY KEY(idip)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE agence (codeagence VARCHAR(4) NOT NULL, libagence VARCHAR(100) NOT NULL, telagence VARCHAR(25) DEFAULT NULL, adresseagence LONGTEXT NOT NULL, etatagence INT NOT NULL, datecreation DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', suppr INT NOT NULL, PRIMARY KEY(codeagence)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE article (idarticle INT AUTO_INCREMENT NOT NULL, idrubrique INT DEFAULT NULL, titrearticle VARCHAR(100) DEFAULT NULL, introtextearticle LONGTEXT DEFAULT NULL, descriptionarticle LONGTEXT DEFAULT NULL, statutarticle INT DEFAULT NULL, urlarticle VARCHAR(255) DEFAULT NULL, referencearticle VARCHAR(255) DEFAULT NULL, corbeillearticle INT DEFAULT NULL, archivearticle INT DEFAULT NULL, lastrubriquearticle INT DEFAULT NULL, compteurarticle INT DEFAULT NULL, articledatepublie DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', articledateajout DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', articledatemodif DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', affichedatepublie INT DEFAULT NULL, afficheauteur INT DEFAULT NULL, afficheaccueil INT DEFAULT NULL, affichereference INT DEFAULT NULL, articledatesupprime DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', articledaterestaure DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', articledatedepublie DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', articledatearchive DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', articledatevalide DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', articlemodifpar INT DEFAULT NULL, articlesupprimepar INT DEFAULT NULL, articleajoutpar INT DEFAULT NULL, articlevalidepar INT DEFAULT NULL, articlearchivepar INT DEFAULT NULL, articledepubliepar INT DEFAULT NULL, articlerestaurepar INT DEFAULT NULL, articlepubliepar INT DEFAULT NULL, ordre INT DEFAULT NULL, typepresentation INT DEFAULT NULL, INDEX IDX_23A0E6625714171 (idrubrique), PRIMARY KEY(idarticle)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE pointer (article_idarticle INT NOT NULL, media_idmedia INT NOT NULL, INDEX IDX_320468A870B0C2C (article_idarticle), INDEX IDX_320468A8DDFE4B5C (media_idmedia), PRIMARY KEY(article_idarticle, media_idmedia)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE positionner (article_idarticle INT NOT NULL, cadre_idcadre INT NOT NULL, INDEX IDX_A04C5BBC70B0C2C (article_idarticle), INDEX IDX_A04C5BBC38B57135 (cadre_idcadre), PRIMARY KEY(article_idarticle, cadre_idcadre)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE avis (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, produit_id INT NOT NULL, note INT NOT NULL, commentaire LONGTEXT NOT NULL, date_avis DATETIME NOT NULL, INDEX IDX_8F91ABF0A76ED395 (user_id), INDEX IDX_8F91ABF0F347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE cadre (idcadre INT AUTO_INCREMENT NOT NULL, idtypecadre INT DEFAULT NULL, libcadre VARCHAR(100) NOT NULL, contenucadre LONGTEXT DEFAULT NULL, positioncadre INT DEFAULT NULL, naturecadre INT DEFAULT NULL, cadreajoutpar INT DEFAULT NULL, cadremodifpar INT DEFAULT NULL, cadredateajout DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', cadredatemodif DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', etatcadre INT NOT NULL, rubpointer INT DEFAULT NULL, articlepointer INT DEFAULT NULL, INDEX IDX_F42587B9149B5F (idtypecadre), PRIMARY KEY(idcadre)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE categoriecompte (codecategorie VARCHAR(4) NOT NULL, libcategorie VARCHAR(50) NOT NULL, active TINYINT(1) NOT NULL, sicarte TINYINT(1) NOT NULL, sicheque TINYINT(1) NOT NULL, PRIMARY KEY(codecategorie)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE chargement (idchargement INT AUTO_INCREMENT NOT NULL, idtypecompte INT DEFAULT NULL, numerofichier VARCHAR(20) NOT NULL, libellefichier VARCHAR(255) NOT NULL, etat INT NOT NULL, archive INT DEFAULT NULL, typechargement INT DEFAULT NULL, naturechargement INT DEFAULT NULL, datedeb DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', datefin DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', filedateajout DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_36328758866B2431 (idtypecompte), PRIMARY KEY(idchargement)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, date_commande DATETIME NOT NULL, statut VARCHAR(255) NOT NULL, total DOUBLE PRECISION NOT NULL, INDEX IDX_6EEAA67DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE commande_produit (id INT AUTO_INCREMENT NOT NULL, commande_id INT NOT NULL, produit_id INT NOT NULL, quantite INT NOT NULL, prix_unitaire DOUBLE PRECISION NOT NULL, INDEX IDX_DF1E9E8782EA2E54 (commande_id), INDEX IDX_DF1E9E87F347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE compte (idcompte INT AUTO_INCREMENT NOT NULL, idtypecompte INT NOT NULL, idabonne INT NOT NULL, numerocompte VARCHAR(20) NOT NULL, libellecompte VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_CFF65260EA9195C1 (numerocompte), INDEX IDX_CFF65260866B2431 (idtypecompte), INDEX IDX_CFF65260128F265C (idabonne), PRIMARY KEY(idcompte)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE compte_export (id INT AUTO_INCREMENT NOT NULL, compte VARCHAR(32) NOT NULL, lib VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE compteinexistant (idcompteinexistant INT AUTO_INCREMENT NOT NULL, idtypecompte INT DEFAULT NULL, numerocompte VARCHAR(20) NOT NULL, libellecompte VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, datecreation DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_390131B866B2431 (idtypecompte), PRIMARY KEY(idcompteinexistant)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE controleur (idcontroleur INT AUTO_INCREMENT NOT NULL, nomcontroleur VARCHAR(150) NOT NULL, description LONGTEXT DEFAULT NULL, client TINYINT(1) NOT NULL, PRIMARY KEY(idcontroleur)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE devise (iddevise INT AUTO_INCREMENT NOT NULL, codedevise VARCHAR(5) NOT NULL, libdevise VARCHAR(40) NOT NULL, valdeviselocal NUMERIC(15, 5) DEFAULT NULL, valdeviselocalachat NUMERIC(15, 5) DEFAULT NULL, locale TINYINT(1) NOT NULL, affiche TINYINT(1) NOT NULL, urlicone VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_43EDA4DF2580510F (codedevise), PRIMARY KEY(iddevise)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE dimension (iddimension INT AUTO_INCREMENT NOT NULL, libdimension VARCHAR(70) NOT NULL, hauteur INT NOT NULL, largeur INT NOT NULL, PRIMARY KEY(iddimension)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE droit (id INT AUTO_INCREMENT NOT NULL, idprofil INT NOT NULL, droits LONGTEXT NOT NULL, INDEX IDX_CB7AA751826B1F3B (idprofil), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE droitclient (id INT AUTO_INCREMENT NOT NULL, idprofil INT NOT NULL, droits LONGTEXT NOT NULL, INDEX IDX_5BE9A5D8826B1F3B (idprofil), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE emplacement (idemplacement INT AUTO_INCREMENT NOT NULL, idcadre INT NOT NULL, libemplacement LONGTEXT DEFAULT NULL, statutemplacement INT NOT NULL, suppr TINYINT(1) NOT NULL, INDEX IDX_C0CF65F62220EBA0 (idcadre), PRIMARY KEY(idemplacement)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE envoi (idenvoi INT AUTO_INCREMENT NOT NULL, idutilisateur INT DEFAULT NULL, idmessageclient INT DEFAULT NULL, idabonne INT DEFAULT NULL, destutil INT DEFAULT NULL, destab INT DEFAULT NULL, statutmsg INT DEFAULT NULL, statutmsgenvoye INT DEFAULT NULL, msgparent INT DEFAULT NULL, msglu TINYINT(1) NOT NULL, typeenvoi INT DEFAULT NULL, dateenvoimsg DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', typemessage INT NOT NULL, INDEX IDX_CA7E3566DBDD131C (idutilisateur), INDEX IDX_CA7E3566CB4000F9 (idmessageclient), INDEX IDX_CA7E3566128F265C (idabonne), PRIMARY KEY(idenvoi)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE ext_translations (id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(8) NOT NULL, object_class VARCHAR(191) NOT NULL, field VARCHAR(32) NOT NULL, foreign_key VARCHAR(64) NOT NULL, content LONGTEXT DEFAULT NULL, INDEX translations_lookup_idx (locale, object_class, foreign_key), INDEX general_translations_lookup_idx (object_class, foreign_key), UNIQUE INDEX lookup_unique_idx (locale, object_class, field, foreign_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB ROW_FORMAT = DYNAMIC
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE facturation (idfacturation INT AUTO_INCREMENT NOT NULL, montantuweb INT NOT NULL, montantafbw INT NOT NULL, PRIMARY KEY(idfacturation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE fonds (idfonds INT AUTO_INCREMENT NOT NULL, idutilisateur INT DEFAULT NULL, codefonds VARCHAR(10) NOT NULL, libfonds VARCHAR(100) NOT NULL, etatfonds INT NOT NULL, suppr TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_39C1FAAA520FE070 (codefonds), INDEX IDX_39C1FAAADBDD131C (idutilisateur), PRIMARY KEY(idfonds)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE groupemenu (idgroupemenu INT AUTO_INCREMENT NOT NULL, libgroupemenu VARCHAR(50) NOT NULL, commentairegroupemenu LONGTEXT DEFAULT NULL, visibiletegroupemenu VARCHAR(255) NOT NULL, PRIMARY KEY(idgroupemenu)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE historique (idconnexion INT AUTO_INCREMENT NOT NULL, iduser INT DEFAULT NULL, idabonne INT DEFAULT NULL, datedeb DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', datefin DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', adresseip VARCHAR(45) DEFAULT NULL, lieu VARCHAR(100) DEFAULT NULL, duree VARCHAR(100) DEFAULT NULL, INDEX IDX_EDBFD5EC5E5C27E9 (iduser), INDEX IDX_EDBFD5EC128F265C (idabonne), PRIMARY KEY(idconnexion)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE infosafterload (idinfos INT AUTO_INCREMENT NOT NULL, datestat DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', typecompte INT NOT NULL, nbretotal INT NOT NULL, nbreimport INT NOT NULL, prcentimport NUMERIC(5, 2) NOT NULL, nbrecpteinexistant INT NOT NULL, idfile INT NOT NULL, PRIMARY KEY(idinfos)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE internaute (mailinternaute VARCHAR(180) NOT NULL, idpays INT DEFAULT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, tel VARCHAR(30) DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, typeinternaute INT NOT NULL, dateinscription DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', etat INT NOT NULL, objet VARCHAR(255) DEFAULT NULL, message LONGTEXT DEFAULT NULL, date_envoi DATETIME DEFAULT NULL, INDEX IDX_6C8E97CCE750CD0E (idpays), PRIMARY KEY(mailinternaute)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE langue (idlangue INT AUTO_INCREMENT NOT NULL, liblangue VARCHAR(255) NOT NULL, codelangue VARCHAR(5) NOT NULL, langueetat TINYINT(1) NOT NULL, iconelangue VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_9357758EF53A805E (codelangue), PRIMARY KEY(idlangue)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE listediffusion (idliste INT AUTO_INCREMENT NOT NULL, nomlistediffusion VARCHAR(100) NOT NULL, actif TINYINT(1) NOT NULL, lesMails LONGTEXT NOT NULL, typeliste INT NOT NULL, PRIMARY KEY(idliste)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE media (idmedia INT AUTO_INCREMENT NOT NULL, iddimension INT DEFAULT NULL, idrubrique INT DEFAULT NULL, idcadre INT DEFAULT NULL, idnaturedoc INT DEFAULT NULL, typemedia INT NOT NULL, urlmedia VARCHAR(255) NOT NULL, urlfistmedia VARCHAR(255) DEFAULT NULL, urlvariable VARCHAR(255) DEFAULT NULL, positionmedia INT DEFAULT NULL, nommedia VARCHAR(255) DEFAULT NULL, descriptionMedia LONGTEXT DEFAULT NULL, illustreImgMedia TINYINT(1) NOT NULL, ajoutmodifmedia INT NOT NULL, mediaajoutpar INT NOT NULL, mediadateajout DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', mediamodifpar INT DEFAULT NULL, mediadatemodif DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_6A2CA10CB4806869 (iddimension), INDEX IDX_6A2CA10C25714171 (idrubrique), INDEX IDX_6A2CA10C2220EBA0 (idcadre), INDEX IDX_6A2CA10CA5D0BFEA (idnaturedoc), PRIMARY KEY(idmedia)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE menu (idmenu INT AUTO_INCREMENT NOT NULL, idarticle INT DEFAULT NULL, idgroupemenu INT DEFAULT NULL, idparentmenu INT DEFAULT NULL, libmenu VARCHAR(50) NOT NULL, typemenu INT NOT NULL, urlexternemenu VARCHAR(255) DEFAULT NULL, menuajoutpar INT DEFAULT NULL, menudateAjout DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', menumodifpar INT DEFAULT NULL, menudatemodif DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_7D053A93DD3E5C08 (idarticle), INDEX IDX_7D053A937267DCFE (idgroupemenu), INDEX IDX_7D053A93C6645D36 (idparentmenu), PRIMARY KEY(idmenu)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE message (idmessage INT AUTO_INCREMENT NOT NULL, mailinternaute VARCHAR(180) DEFAULT NULL, idservice INT DEFAULT NULL, titremessage VARCHAR(150) NOT NULL, contenu LONGTEXT NOT NULL, dateenvoi DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', corbeillemessage TINYINT(1) NOT NULL, messagelu TINYINT(1) NOT NULL, INDEX IDX_B6BD307F59C828B2 (mailinternaute), INDEX IDX_B6BD307F3E99C8BC (idservice), PRIMARY KEY(idmessage)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messageclient (idmessageclient INT AUTO_INCREMENT NOT NULL, objetmessageclient VARCHAR(150) NOT NULL, contenumessageclient LONGTEXT DEFAULT NULL, messagesysteme TINYINT(1) NOT NULL, date_message DATETIME NOT NULL, PRIMARY KEY(idmessageclient)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messagereponse (idmessagereponse INT AUTO_INCREMENT NOT NULL, idmessage INT DEFAULT NULL, iduser INT DEFAULT NULL, datereponse DATETIME NOT NULL, contenu LONGTEXT NOT NULL, titreMessage VARCHAR(100) NOT NULL, contenuMessage LONGTEXT NOT NULL, dateEnvoi DATETIME NOT NULL, messageLu INT NOT NULL, destinataireMsg LONGTEXT NOT NULL, INDEX IDX_91A2A44B69B96211 (idmessage), INDEX IDX_91A2A44B5E5C27E9 (iduser), PRIMARY KEY(idmessagereponse)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE module (idmodule INT AUTO_INCREMENT NOT NULL, libmodule VARCHAR(100) NOT NULL, client TINYINT(1) NOT NULL, ordre INT DEFAULT NULL, PRIMARY KEY(idmodule)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE naturedoc (idnaturedoc INT AUTO_INCREMENT NOT NULL, libnaturedoc VARCHAR(100) NOT NULL, statutnaturedoc INT DEFAULT NULL, naturedocajoutpar INT DEFAULT NULL, naturedocmodifpar INT DEFAULT NULL, naturedocactivepar INT DEFAULT NULL, naturedocdesactivepar INT DEFAULT NULL, naturedocdateajout DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', naturedocdatemodif DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', naturedocdatedesactive DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', naturedocdateactive DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(idnaturedoc)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE newsletter (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE objet (idobjet INT AUTO_INCREMENT NOT NULL, libobjet VARCHAR(100) NOT NULL, descriptionobjet LONGTEXT DEFAULT NULL, etatobjet TINYINT(1) NOT NULL, objetajoutpar INT DEFAULT NULL, objetmodifpar INT DEFAULT NULL, objetdateajout DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', objetdatemodif DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', PRIMARY KEY(idobjet)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE operation (idoperation INT AUTO_INCREMENT NOT NULL, idcompte INT DEFAULT NULL, libelleoperation VARCHAR(100) NOT NULL, dateoperation DATETIME NOT NULL, montant NUMERIC(10, 2) NOT NULL, INDEX IDX_1981A66DAB4BFFCC (idcompte), PRIMARY KEY(idoperation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE operationcfonb (id INT AUTO_INCREMENT NOT NULL, numerocompte VARCHAR(20) NOT NULL, iddevise INT NOT NULL, liboperation VARCHAR(100) NOT NULL, datevaleur DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', dateoperation DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', datecompta DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', montant NUMERIC(14, 2) NOT NULL, sensoperation VARCHAR(2) NOT NULL, coef INT DEFAULT NULL, numeromvt VARCHAR(15) NOT NULL, codoperation VARCHAR(5) NOT NULL, periode VARCHAR(10) NOT NULL, traite TINYINT(1) NOT NULL, idfile INT NOT NULL, soldeligne NUMERIC(14, 2) DEFAULT NULL, chrgjr TINYINT(1) NOT NULL, ordre INT NOT NULL, mttafbw VARCHAR(14) DEFAULT NULL, bnqcod VARCHAR(5) DEFAULT NULL, guichet VARCHAR(5) DEFAULT NULL, ladevise VARCHAR(3) DEFAULT NULL, motrej VARCHAR(2) DEFAULT NULL, monori VARCHAR(1) DEFAULT NULL, virgul VARCHAR(1) DEFAULT NULL, res21 VARCHAR(4) DEFAULT NULL, exocom VARCHAR(1) DEFAULT NULL, ind VARCHAR(1) DEFAULT NULL, res22 VARCHAR(2) DEFAULT NULL, noecri VARCHAR(7) DEFAULT NULL, cdafb VARCHAR(2) DEFAULT NULL, res23 VARCHAR(2) DEFAULT NULL, res13 VARCHAR(2) DEFAULT NULL, cdcoib VARCHAR(4) DEFAULT NULL, sign VARCHAR(1) DEFAULT NULL, cdexo VARCHAR(1) DEFAULT NULL, INDEX IDX_6FE1B595EA9195C1 (numerocompte), INDEX IDX_6FE1B59527500973 (iddevise), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE ordreclient (idordre INT AUTO_INCREMENT NOT NULL, nomtable VARCHAR(100) NOT NULL, ordre JSON NOT NULL, PRIMARY KEY(idordre)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE parametrage (idparam INT AUTO_INCREMENT NOT NULL, paramTitre VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, valeur LONGTEXT DEFAULT NULL, paramtype INT NOT NULL, typedescription VARCHAR(255) DEFAULT NULL, actif TINYINT(1) NOT NULL, idgroupe INT DEFAULT NULL, grpedescription LONGTEXT DEFAULT NULL, paramajoutpar INT DEFAULT NULL, PRIMARY KEY(idparam)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE paramsysteme (idparam INT AUTO_INCREMENT NOT NULL, cle VARCHAR(100) NOT NULL, valeur LONGTEXT DEFAULT NULL, description LONGTEXT DEFAULT NULL, idtype INT NOT NULL, suppr TINYINT(1) NOT NULL, etatparametre TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_349AC9D641401D17 (cle), PRIMARY KEY(idparam)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE pays (idpays INT AUTO_INCREMENT NOT NULL, libellepays VARCHAR(50) NOT NULL, code VARCHAR(3) NOT NULL, PRIMARY KEY(idpays)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, categorie_id INT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, prix DOUBLE PRECISION NOT NULL, stock INT NOT NULL, image VARCHAR(255) NOT NULL, INDEX IDX_29A5EC27BCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE profil (idprofil INT AUTO_INCREMENT NOT NULL, libprofil VARCHAR(70) NOT NULL, etatprofil TINYINT(1) NOT NULL, PRIMARY KEY(idprofil)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE profilclient (idprofil INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(idprofil)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE recherche (idrecherche INT AUTO_INCREMENT NOT NULL, motcle VARCHAR(70) NOT NULL, PRIMARY KEY(idrecherche)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE rubrique (idrubrique INT AUTO_INCREMENT NOT NULL, idparent INT DEFAULT NULL, nomrubrique VARCHAR(100) NOT NULL, descriptionrubrique LONGTEXT DEFAULT NULL, typepresentation INT DEFAULT NULL, typerubrique INT NOT NULL, isfaq TINYINT(1) DEFAULT NULL, urlicone VARCHAR(255) DEFAULT NULL, rubriqueajoutpar INT DEFAULT NULL, rubriquemodifpar INT DEFAULT NULL, rubriqueDateAjout DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', rubriqueDateModif DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_8FA4097C5933CDE3 (idparent), PRIMARY KEY(idrubrique)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE cadresrubrique (idrubrique INT NOT NULL, idcadre INT NOT NULL, INDEX IDX_6B1E8A9A25714171 (idrubrique), INDEX IDX_6B1E8A9A2220EBA0 (idcadre), PRIMARY KEY(idrubrique, idcadre)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE service (idservice INT AUTO_INCREMENT NOT NULL, libelleservice VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, prix NUMERIC(10, 2) NOT NULL, etat INT NOT NULL, PRIMARY KEY(idservice)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE soldecompte (idsoldecompte INT AUTO_INCREMENT NOT NULL, idcompte INT DEFAULT NULL, montant NUMERIC(10, 2) NOT NULL, datesolde DATETIME NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_65E79693AB4BFFCC (idcompte), PRIMARY KEY(idsoldecompte)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE sondage (idsondage INT AUTO_INCREMENT NOT NULL, question LONGTEXT NOT NULL, actif TINYINT(1) NOT NULL, questionajoutpar INT DEFAULT NULL, questiondateajout DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', PRIMARY KEY(idsondage)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE sondageopinion (idopinion INT AUTO_INCREMENT NOT NULL, idsondage INT NOT NULL, reponse VARCHAR(255) NOT NULL, nbreponse INT NOT NULL, INDEX IDX_E71AF83BAA7D9AF1 (idsondage), PRIMARY KEY(idopinion)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE squelettepage (id INT AUTO_INCREMENT NOT NULL, page VARCHAR(255) NOT NULL, pageurl VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE statistique (idstat INT AUTO_INCREMENT NOT NULL, libstat VARCHAR(100) NOT NULL, typestat INT NOT NULL, etatstat TINYINT(1) NOT NULL, descriptiontype LONGTEXT DEFAULT NULL, valeur INT NOT NULL, route VARCHAR(255) NOT NULL, ecart VARCHAR(255) DEFAULT NULL, parametres JSON NOT NULL, ordre INT NOT NULL, PRIMARY KEY(idstat)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE statistiqueclient (idstat INT AUTO_INCREMENT NOT NULL, libstat VARCHAR(100) NOT NULL, typestat INT NOT NULL, etatstat TINYINT(1) NOT NULL, descriptiontype LONGTEXT DEFAULT NULL, valeur INT NOT NULL, route VARCHAR(255) NOT NULL, ecart VARCHAR(255) DEFAULT NULL, parametres JSON NOT NULL, ordre INT NOT NULL, PRIMARY KEY(idstat)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE typecadre (idtypecadre INT AUTO_INCREMENT NOT NULL, libTypeCadre VARCHAR(100) NOT NULL, PRIMARY KEY(idtypecadre)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE typecompte (idtypecompte INT AUTO_INCREMENT NOT NULL, libelletypecompte VARCHAR(50) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(idtypecompte)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE typeoperation (idtypeoperation INT AUTO_INCREMENT NOT NULL, libelletypeoperation VARCHAR(50) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(idtypeoperation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, idabonne INT DEFAULT NULL, idprofil INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, ville VARCHAR(255) NOT NULL, code_postal VARCHAR(255) NOT NULL, pays VARCHAR(255) NOT NULL, url_photo VARCHAR(170) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649128F265C (idabonne), INDEX IDX_8D93D649826B1F3B (idprofil), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonne ADD CONSTRAINT FK_76328BF0826B1F3B FOREIGN KEY (idprofil) REFERENCES profilclient (idprofil)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonne ADD CONSTRAINT FK_76328BF0A2DBA314 FOREIGN KEY (idabonneparent) REFERENCES abonne (idabonne)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonne ADD CONSTRAINT FK_76328BF059C828B2 FOREIGN KEY (mailinternaute) REFERENCES internaute (mailinternaute)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE adresse ADD CONSTRAINT FK_C35F0816A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE adresseip ADD CONSTRAINT FK_B7A04D977406E249 FOREIGN KEY (idopinion) REFERENCES sondageopinion (idopinion)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article ADD CONSTRAINT FK_23A0E6625714171 FOREIGN KEY (idrubrique) REFERENCES rubrique (idrubrique)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE pointer ADD CONSTRAINT FK_320468A870B0C2C FOREIGN KEY (article_idarticle) REFERENCES article (idarticle)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE pointer ADD CONSTRAINT FK_320468A8DDFE4B5C FOREIGN KEY (media_idmedia) REFERENCES media (idmedia)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE positionner ADD CONSTRAINT FK_A04C5BBC70B0C2C FOREIGN KEY (article_idarticle) REFERENCES article (idarticle)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE positionner ADD CONSTRAINT FK_A04C5BBC38B57135 FOREIGN KEY (cadre_idcadre) REFERENCES cadre (idcadre)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cadre ADD CONSTRAINT FK_F42587B9149B5F FOREIGN KEY (idtypecadre) REFERENCES typecadre (idtypecadre)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chargement ADD CONSTRAINT FK_36328758866B2431 FOREIGN KEY (idtypecompte) REFERENCES typecompte (idtypecompte)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande_produit ADD CONSTRAINT FK_DF1E9E8782EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande_produit ADD CONSTRAINT FK_DF1E9E87F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE compte ADD CONSTRAINT FK_CFF65260866B2431 FOREIGN KEY (idtypecompte) REFERENCES typecompte (idtypecompte)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE compte ADD CONSTRAINT FK_CFF65260128F265C FOREIGN KEY (idabonne) REFERENCES abonne (idabonne)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE compteinexistant ADD CONSTRAINT FK_390131B866B2431 FOREIGN KEY (idtypecompte) REFERENCES typecompte (idtypecompte)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE droit ADD CONSTRAINT FK_CB7AA751826B1F3B FOREIGN KEY (idprofil) REFERENCES profil (idprofil)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE droitclient ADD CONSTRAINT FK_5BE9A5D8826B1F3B FOREIGN KEY (idprofil) REFERENCES profilclient (idprofil)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE emplacement ADD CONSTRAINT FK_C0CF65F62220EBA0 FOREIGN KEY (idcadre) REFERENCES cadre (idcadre)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE envoi ADD CONSTRAINT FK_CA7E3566DBDD131C FOREIGN KEY (idutilisateur) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE envoi ADD CONSTRAINT FK_CA7E3566CB4000F9 FOREIGN KEY (idmessageclient) REFERENCES messageclient (idmessageclient)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE envoi ADD CONSTRAINT FK_CA7E3566128F265C FOREIGN KEY (idabonne) REFERENCES abonne (idabonne)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE fonds ADD CONSTRAINT FK_39C1FAAADBDD131C FOREIGN KEY (idutilisateur) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE historique ADD CONSTRAINT FK_EDBFD5EC5E5C27E9 FOREIGN KEY (iduser) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE historique ADD CONSTRAINT FK_EDBFD5EC128F265C FOREIGN KEY (idabonne) REFERENCES abonne (idabonne)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE internaute ADD CONSTRAINT FK_6C8E97CCE750CD0E FOREIGN KEY (idpays) REFERENCES pays (idpays)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CB4806869 FOREIGN KEY (iddimension) REFERENCES dimension (iddimension)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C25714171 FOREIGN KEY (idrubrique) REFERENCES rubrique (idrubrique)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C2220EBA0 FOREIGN KEY (idcadre) REFERENCES cadre (idcadre)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CA5D0BFEA FOREIGN KEY (idnaturedoc) REFERENCES naturedoc (idnaturedoc)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE menu ADD CONSTRAINT FK_7D053A93DD3E5C08 FOREIGN KEY (idarticle) REFERENCES article (idarticle)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE menu ADD CONSTRAINT FK_7D053A937267DCFE FOREIGN KEY (idgroupemenu) REFERENCES groupemenu (idgroupemenu)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE menu ADD CONSTRAINT FK_7D053A93C6645D36 FOREIGN KEY (idparentmenu) REFERENCES menu (idmenu)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD CONSTRAINT FK_B6BD307F59C828B2 FOREIGN KEY (mailinternaute) REFERENCES internaute (mailinternaute)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD CONSTRAINT FK_B6BD307F3E99C8BC FOREIGN KEY (idservice) REFERENCES service (idservice)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messagereponse ADD CONSTRAINT FK_91A2A44B69B96211 FOREIGN KEY (idmessage) REFERENCES message (idmessage)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messagereponse ADD CONSTRAINT FK_91A2A44B5E5C27E9 FOREIGN KEY (iduser) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE operation ADD CONSTRAINT FK_1981A66DAB4BFFCC FOREIGN KEY (idcompte) REFERENCES compte (idcompte)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE operationcfonb ADD CONSTRAINT FK_6FE1B595EA9195C1 FOREIGN KEY (numerocompte) REFERENCES compte (numerocompte)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE operationcfonb ADD CONSTRAINT FK_6FE1B59527500973 FOREIGN KEY (iddevise) REFERENCES devise (iddevise)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rubrique ADD CONSTRAINT FK_8FA4097C5933CDE3 FOREIGN KEY (idparent) REFERENCES rubrique (idrubrique) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cadresrubrique ADD CONSTRAINT FK_6B1E8A9A25714171 FOREIGN KEY (idrubrique) REFERENCES rubrique (idrubrique)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cadresrubrique ADD CONSTRAINT FK_6B1E8A9A2220EBA0 FOREIGN KEY (idcadre) REFERENCES cadre (idcadre)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE soldecompte ADD CONSTRAINT FK_65E79693AB4BFFCC FOREIGN KEY (idcompte) REFERENCES compte (idcompte)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sondageopinion ADD CONSTRAINT FK_E71AF83BAA7D9AF1 FOREIGN KEY (idsondage) REFERENCES sondage (idsondage)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649128F265C FOREIGN KEY (idabonne) REFERENCES abonne (idabonne)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649826B1F3B FOREIGN KEY (idprofil) REFERENCES profilclient (idprofil)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE abonne DROP FOREIGN KEY FK_76328BF0826B1F3B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonne DROP FOREIGN KEY FK_76328BF0A2DBA314
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonne DROP FOREIGN KEY FK_76328BF059C828B2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE adresse DROP FOREIGN KEY FK_C35F0816A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE adresseip DROP FOREIGN KEY FK_B7A04D977406E249
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article DROP FOREIGN KEY FK_23A0E6625714171
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE pointer DROP FOREIGN KEY FK_320468A870B0C2C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE pointer DROP FOREIGN KEY FK_320468A8DDFE4B5C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE positionner DROP FOREIGN KEY FK_A04C5BBC70B0C2C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE positionner DROP FOREIGN KEY FK_A04C5BBC38B57135
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0F347EFB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cadre DROP FOREIGN KEY FK_F42587B9149B5F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chargement DROP FOREIGN KEY FK_36328758866B2431
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande_produit DROP FOREIGN KEY FK_DF1E9E8782EA2E54
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande_produit DROP FOREIGN KEY FK_DF1E9E87F347EFB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE compte DROP FOREIGN KEY FK_CFF65260866B2431
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE compte DROP FOREIGN KEY FK_CFF65260128F265C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE compteinexistant DROP FOREIGN KEY FK_390131B866B2431
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE droit DROP FOREIGN KEY FK_CB7AA751826B1F3B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE droitclient DROP FOREIGN KEY FK_5BE9A5D8826B1F3B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE emplacement DROP FOREIGN KEY FK_C0CF65F62220EBA0
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE envoi DROP FOREIGN KEY FK_CA7E3566DBDD131C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE envoi DROP FOREIGN KEY FK_CA7E3566CB4000F9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE envoi DROP FOREIGN KEY FK_CA7E3566128F265C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE fonds DROP FOREIGN KEY FK_39C1FAAADBDD131C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE historique DROP FOREIGN KEY FK_EDBFD5EC5E5C27E9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE historique DROP FOREIGN KEY FK_EDBFD5EC128F265C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE internaute DROP FOREIGN KEY FK_6C8E97CCE750CD0E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10CB4806869
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C25714171
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C2220EBA0
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10CA5D0BFEA
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE menu DROP FOREIGN KEY FK_7D053A93DD3E5C08
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE menu DROP FOREIGN KEY FK_7D053A937267DCFE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE menu DROP FOREIGN KEY FK_7D053A93C6645D36
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F59C828B2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F3E99C8BC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messagereponse DROP FOREIGN KEY FK_91A2A44B69B96211
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messagereponse DROP FOREIGN KEY FK_91A2A44B5E5C27E9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE operation DROP FOREIGN KEY FK_1981A66DAB4BFFCC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE operationcfonb DROP FOREIGN KEY FK_6FE1B595EA9195C1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE operationcfonb DROP FOREIGN KEY FK_6FE1B59527500973
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27BCF5E72D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rubrique DROP FOREIGN KEY FK_8FA4097C5933CDE3
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cadresrubrique DROP FOREIGN KEY FK_6B1E8A9A25714171
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cadresrubrique DROP FOREIGN KEY FK_6B1E8A9A2220EBA0
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE soldecompte DROP FOREIGN KEY FK_65E79693AB4BFFCC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sondageopinion DROP FOREIGN KEY FK_E71AF83BAA7D9AF1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649128F265C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649826B1F3B
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE abonne
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE action
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE adresse
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE adresseip
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE agence
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE article
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE pointer
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE positionner
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE avis
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE cadre
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE categorie
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE categoriecompte
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE chargement
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE commande
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE commande_produit
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE compte
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE compte_export
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE compteinexistant
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE controleur
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE devise
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE dimension
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE droit
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE droitclient
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE emplacement
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE envoi
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE ext_translations
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE facturation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE fonds
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE groupemenu
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE historique
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE infosafterload
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE internaute
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE langue
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE listediffusion
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE media
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE menu
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE message
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messageclient
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messagereponse
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE module
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE naturedoc
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE newsletter
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE objet
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE operation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE operationcfonb
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE ordreclient
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE parametrage
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE paramsysteme
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE pays
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE produit
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE profil
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE profilclient
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE recherche
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE rubrique
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE cadresrubrique
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE service
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE soldecompte
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE sondage
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE sondageopinion
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE squelettepage
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE statistique
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE statistiqueclient
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE typecadre
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE typecompte
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE typeoperation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `user`
        SQL);
    }
}
