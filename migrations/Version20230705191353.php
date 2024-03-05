<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230705191353 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activite DROP FOREIGN KEY FK_B87555157301286E');
        $this->addSql('ALTER TABLE activite DROP FOREIGN KEY FK_B8755515F67B2066');
        $this->addSql('ALTER TABLE activite DROP FOREIGN KEY FK_B875551569C24F6');
        $this->addSql('ALTER TABLE branches_table DROP FOREIGN KEY FK_82C65C0A6635C9C2');
        $this->addSql('ALTER TABLE champs DROP FOREIGN KEY FK_B34671BE1ABA8B');
        $this->addSql('ALTER TABLE champs DROP FOREIGN KEY FK_B34671BE5FF69B7D');
        $this->addSql('ALTER TABLE competence_profil DROP FOREIGN KEY FK_AF35D78BAB5ECCCE');
        $this->addSql('ALTER TABLE competence_profil DROP FOREIGN KEY FK_AF35D78BA76B6C5F');
        $this->addSql('ALTER TABLE contact_donneur_ordre DROP FOREIGN KEY FK_B6DC8AA2D0C4FDF3');
        $this->addSql('ALTER TABLE corres_colu DROP FOREIGN KEY FK_DF3952496635C9C2');
        $this->addSql('ALTER TABLE critere_model_facturation DROP FOREIGN KEY FK_EE4ECA5EDEC978EE');
        $this->addSql('ALTER TABLE detail_competence DROP FOREIGN KEY FK_2FCED8A9AB5ECCCE');
        $this->addSql('ALTER TABLE detail_competence DROP FOREIGN KEY FK_2FCED8A969C24F6');
        $this->addSql('ALTER TABLE detail_model_affichage DROP FOREIGN KEY FK_36F7E8CCEDF52E15');
        $this->addSql('ALTER TABLE etap_activite DROP FOREIGN KEY FK_2C4B5DFF831D4546');
        $this->addSql('ALTER TABLE etap_activite DROP FOREIGN KEY FK_2C4B5DFF69C24F6');
        $this->addSql('ALTER TABLE group_profil DROP FOREIGN KEY FK_35506A47A76B6C5F');
        $this->addSql('ALTER TABLE group_profil DROP FOREIGN KEY FK_35506A47AE8F35D2');
        $this->addSql('ALTER TABLE import DROP FOREIGN KEY FK_9D4ECE1D29AE5B72');
        $this->addSql('ALTER TABLE import DROP FOREIGN KEY FK_9D4ECE1DA3E460EC');
        $this->addSql('ALTER TABLE import_donneur_ordre_back DROP FOREIGN KEY FK_5D8AEDD73697DFFE');
        $this->addSql('ALTER TABLE interm_param_etap DROP FOREIGN KEY FK_C21DE3385CA1B8D8');
        $this->addSql('ALTER TABLE interm_param_etap DROP FOREIGN KEY FK_C21DE33883F508AD');
        $this->addSql('ALTER TABLE interm_param_sous_etap DROP FOREIGN KEY FK_5C2507EB82F41C97');
        $this->addSql('ALTER TABLE interm_param_sous_etap DROP FOREIGN KEY FK_5C2507EB69C24F6');
        $this->addSql('ALTER TABLE interm_resultat_activite DROP FOREIGN KEY FK_87F4495B831D4546');
        $this->addSql('ALTER TABLE interm_resultat_activite DROP FOREIGN KEY FK_87F4495BCA2124AB');
        $this->addSql('ALTER TABLE param_activite DROP FOREIGN KEY FK_E53F14D138551A49');
        $this->addSql('ALTER TABLE portefeuille DROP FOREIGN KEY FK_2955FFFECBD6E4BE');
        $this->addSql('ALTER TABLE portefeuille DROP FOREIGN KEY FK_2955FFFED0C4FDF3');
        $this->addSql('ALTER TABLE regle_model_facturation DROP FOREIGN KEY FK_E19E07F729AE5B72');
        $this->addSql('ALTER TABLE resultat_activite DROP FOREIGN KEY FK_EED3238A69C24F6');
        $this->addSql('ALTER TABLE resultat_activite DROP FOREIGN KEY FK_EED3238A831D4546');
        $this->addSql('ALTER TABLE roles DROP FOREIGN KEY FK_B63E2EC789E8BDC');
        $this->addSql('ALTER TABLE roles DROP FOREIGN KEY FK_B63E2EC7A76B6C5F');
        $this->addSql('ALTER TABLE sous_etap_activite DROP FOREIGN KEY FK_112C58705CA1B8D8');
        $this->addSql('ALTER TABLE utilisateurs DROP FOREIGN KEY FK_497B315EAE8F35D2');
        $this->addSql('DROP TABLE activite');
        $this->addSql('DROP TABLE activite_parent');
        $this->addSql('DROP TABLE branches_table');
        $this->addSql('DROP TABLE champs');
        $this->addSql('DROP TABLE columns_params');
        $this->addSql('DROP TABLE competence');
        $this->addSql('DROP TABLE competence_profil');
        $this->addSql('DROP TABLE contact_donneur_ordre');
        $this->addSql('DROP TABLE corres_colu');
        $this->addSql('DROP TABLE creance');
        $this->addSql('DROP TABLE critere_model_facturation');
        $this->addSql('DROP TABLE detail_competence');
        $this->addSql('DROP TABLE detail_model_affichage');
        $this->addSql('DROP TABLE donneur_ordre');
        $this->addSql('DROP TABLE etap_activite');
        $this->addSql('DROP TABLE etat_activite');
        $this->addSql('DROP TABLE groupe');
        $this->addSql('DROP TABLE group_profil');
        $this->addSql('DROP TABLE import');
        $this->addSql('DROP TABLE import_donneur_ordre_back');
        $this->addSql('DROP TABLE integration');
        $this->addSql('DROP TABLE interm_param_etap');
        $this->addSql('DROP TABLE interm_param_sous_etap');
        $this->addSql('DROP TABLE interm_resultat_activite');
        $this->addSql('DROP TABLE listes_roles');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('DROP TABLE model_affichage');
        $this->addSql('DROP TABLE model_facturation');
        $this->addSql('DROP TABLE model_import');
        $this->addSql('DROP TABLE param_activite');
        $this->addSql('DROP TABLE portefeuille');
        $this->addSql('DROP TABLE profil');
        $this->addSql('DROP TABLE regle_model_facturation');
        $this->addSql('DROP TABLE resultat_activite');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE sous_etap_activite');
        $this->addSql('DROP TABLE test');
        $this->addSql('DROP TABLE type_parametrage');
        $this->addSql('DROP TABLE utilisateurs');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activite (id INT AUTO_INCREMENT NOT NULL, etat_activite_id INT DEFAULT NULL, id_param_id INT NOT NULL, id_parent_activite_id INT DEFAULT NULL, titre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, code VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date_creation DATETIME NOT NULL, num_link VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, INDEX IDX_B8755515F67B2066 (etat_activite_id), INDEX IDX_B875551569C24F6 (id_param_id), INDEX IDX_B87555157301286E (id_parent_activite_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE activite_parent (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date_creation DATETIME NOT NULL, etat_creation VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE branches_table (id INT AUTO_INCREMENT NOT NULL, id_model_import_id INT NOT NULL, name_branche VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_82C65C0A6635C9C2 (id_model_import_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE champs (id INT AUTO_INCREMENT NOT NULL, form_id INT DEFAULT NULL, champs_id INT DEFAULT NULL, colum_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, value VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, table_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_B34671BE5FF69B7D (form_id), INDEX IDX_B34671BE1ABA8B (champs_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE columns_params (id INT AUTO_INCREMENT NOT NULL, titre_col VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, table_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, code VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE competence (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date_creation DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE competence_profil (id INT AUTO_INCREMENT NOT NULL, id_profil_id INT DEFAULT NULL, id_competence_id INT DEFAULT NULL, status INT NOT NULL, INDEX IDX_AF35D78BAB5ECCCE (id_competence_id), INDEX IDX_AF35D78BA76B6C5F (id_profil_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE contact_donneur_ordre (id INT AUTO_INCREMENT NOT NULL, id_donneur_ordre_id INT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, prenom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, adresse VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, email VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, poste VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, tel VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, mobile VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_B6DC8AA2D0C4FDF3 (id_donneur_ordre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE corres_colu (id INT AUTO_INCREMENT NOT NULL, id_model_import_id INT NOT NULL, table_name VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, column_name VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, code VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, required TINYINT(1) NOT NULL, column_table VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_DF3952496635C9C2 (id_model_import_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE creance (id INT AUTO_INCREMENT NOT NULL, numero_creance VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, montant INT DEFAULT NULL, etat_affichge INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE critere_model_facturation (id INT AUTO_INCREMENT NOT NULL, id_regle_id INT NOT NULL, table_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, column_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, valeur1 VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, valeur2 VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, action VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_EE4ECA5EDEC978EE (id_regle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE detail_competence (id INT AUTO_INCREMENT NOT NULL, id_competence_id INT NOT NULL, id_param_id INT NOT NULL, INDEX IDX_2FCED8A9AB5ECCCE (id_competence_id), INDEX IDX_2FCED8A969C24F6 (id_param_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE detail_model_affichage (id INT AUTO_INCREMENT NOT NULL, id_model_affichage_id INT NOT NULL, table_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, champ_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, length INT NOT NULL, etat INT NOT NULL, type_creance VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, type_champ VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, required TINYINT(1) NOT NULL, INDEX IDX_36F7E8CCEDF52E15 (id_model_affichage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE donneur_ordre (id INT AUTO_INCREMENT NOT NULL, metier VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date_creation DATETIME NOT NULL, date_debut DATETIME NOT NULL, raison_sociale VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, numero_rc VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, cp VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, compte_bancaire VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date_fin DATETIME DEFAULT \'NULL\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE etap_activite (id INT AUTO_INCREMENT NOT NULL, id_activite_id INT DEFAULT NULL, id_param_id INT NOT NULL, etat INT DEFAULT NULL, titre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_2C4B5DFF69C24F6 (id_param_id), INDEX IDX_2C4B5DFF831D4546 (id_activite_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE etat_activite (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE groupe (id INT AUTO_INCREMENT NOT NULL, date_creation DATE NOT NULL, status VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, titre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE group_profil (id INT AUTO_INCREMENT NOT NULL, id_group_id INT DEFAULT NULL, id_profil_id INT DEFAULT NULL, INDEX IDX_35506A47AE8F35D2 (id_group_id), INDEX IDX_35506A47A76B6C5F (id_profil_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE import (id INT AUTO_INCREMENT NOT NULL, id_integration_id INT NOT NULL, id_model_id INT NOT NULL, url VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, etat INT NOT NULL, date_creation DATE NOT NULL, date_execution DATETIME DEFAULT \'NULL\', date_fin_execution DATETIME DEFAULT \'NULL\', INDEX IDX_9D4ECE1DA3E460EC (id_integration_id), INDEX IDX_9D4ECE1D29AE5B72 (id_model_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE import_donneur_ordre_back (id INT AUTO_INCREMENT NOT NULL, id_import_id INT NOT NULL, entete VARCHAR(500) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, etat_exist INT NOT NULL, table_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, id_column VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, type_table VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_5D8AEDD73697DFFE (id_import_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE integration (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date_creation DATE NOT NULL, date_execution DATE DEFAULT \'NULL\', date_fin_execution DATE DEFAULT \'NULL\', etat INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE interm_param_etap (id INT AUTO_INCREMENT NOT NULL, id_etap_id INT DEFAULT NULL, id_param_activite_id INT NOT NULL, etat INT DEFAULT NULL, INDEX IDX_C21DE3385CA1B8D8 (id_etap_id), INDEX IDX_C21DE33883F508AD (id_param_activite_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE interm_param_sous_etap (id INT AUTO_INCREMENT NOT NULL, id_sous_etap_id INT NOT NULL, id_param_id INT NOT NULL, etat INT NOT NULL, INDEX IDX_5C2507EB82F41C97 (id_sous_etap_id), INDEX IDX_5C2507EB69C24F6 (id_param_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE interm_resultat_activite (id INT AUTO_INCREMENT NOT NULL, id_resultat_id INT NOT NULL, id_activite_id INT NOT NULL, INDEX IDX_87F4495BCA2124AB (id_resultat_id), INDEX IDX_87F4495B831D4546 (id_activite_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE listes_roles (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, titre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, headers LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, queue_name VARCHAR(190) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT \'NULL\', INDEX IDX_75EA56E016BA31DB (delivered_at), INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE model_affichage (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date_creation DATETIME NOT NULL, objet VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE model_facturation (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, objet VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, date_creation DATE NOT NULL, test_date DATE DEFAULT \'NULL\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE model_import (id INT AUTO_INCREMENT NOT NULL, date_creation DATETIME NOT NULL, titre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, type VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE param_activite (id INT AUTO_INCREMENT NOT NULL, id_branche_id INT DEFAULT NULL, type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, code_type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_E53F14D138551A49 (id_branche_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE portefeuille (id INT AUTO_INCREMENT NOT NULL, id_donneur_ordre_id INT NOT NULL, id_model_fact_id INT DEFAULT NULL, numero_ptf VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, titre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, type_creance VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date_debut_gestion DATETIME NOT NULL, date_fin_gestion DATETIME NOT NULL, duree_gestion VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, actif INT NOT NULL, type_mission VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_2955FFFED0C4FDF3 (id_donneur_ordre_id), INDEX IDX_2955FFFECBD6E4BE (id_model_fact_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE profil (id INT AUTO_INCREMENT NOT NULL, date_creation DATE NOT NULL, titre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, status INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE regle_model_facturation (id INT AUTO_INCREMENT NOT NULL, id_model_id INT NOT NULL, nom VARCHAR(80) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_E19E07F729AE5B72 (id_model_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE resultat_activite (id INT AUTO_INCREMENT NOT NULL, id_activite_id INT NOT NULL, id_param_id INT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ordre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, numero VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_EED3238A831D4546 (id_activite_id), INDEX IDX_EED3238A69C24F6 (id_param_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE roles (id INT AUTO_INCREMENT NOT NULL, id_role_id INT DEFAULT NULL, id_profil_id INT DEFAULT NULL, status INT NOT NULL, INDEX IDX_B63E2EC789E8BDC (id_role_id), INDEX IDX_B63E2EC7A76B6C5F (id_profil_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE sous_etap_activite (id INT AUTO_INCREMENT NOT NULL, id_etap_id INT NOT NULL, etat INT NOT NULL, titre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_112C58705CA1B8D8 (id_etap_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE test (id INT AUTO_INCREMENT NOT NULL, test DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE type_parametrage (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE utilisateurs (id INT AUTO_INCREMENT NOT NULL, id_group_id INT DEFAULT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, prenom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, status INT NOT NULL, tel VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, imei VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, mobile VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, adresse VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, rayon VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, cin VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ville VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, pays VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, img VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_497B315EAE8F35D2 (id_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE activite ADD CONSTRAINT FK_B87555157301286E FOREIGN KEY (id_parent_activite_id) REFERENCES activite_parent (id)');
        $this->addSql('ALTER TABLE activite ADD CONSTRAINT FK_B8755515F67B2066 FOREIGN KEY (etat_activite_id) REFERENCES etat_activite (id)');
        $this->addSql('ALTER TABLE activite ADD CONSTRAINT FK_B875551569C24F6 FOREIGN KEY (id_param_id) REFERENCES param_activite (id)');
        $this->addSql('ALTER TABLE branches_table ADD CONSTRAINT FK_82C65C0A6635C9C2 FOREIGN KEY (id_model_import_id) REFERENCES model_import (id)');
        $this->addSql('ALTER TABLE champs ADD CONSTRAINT FK_B34671BE1ABA8B FOREIGN KEY (champs_id) REFERENCES detail_model_affichage (id)');
        $this->addSql('ALTER TABLE champs ADD CONSTRAINT FK_B34671BE5FF69B7D FOREIGN KEY (form_id) REFERENCES donneur_ordre (id)');
        $this->addSql('ALTER TABLE competence_profil ADD CONSTRAINT FK_AF35D78BAB5ECCCE FOREIGN KEY (id_competence_id) REFERENCES competence (id)');
        $this->addSql('ALTER TABLE competence_profil ADD CONSTRAINT FK_AF35D78BA76B6C5F FOREIGN KEY (id_profil_id) REFERENCES profil (id)');
        $this->addSql('ALTER TABLE contact_donneur_ordre ADD CONSTRAINT FK_B6DC8AA2D0C4FDF3 FOREIGN KEY (id_donneur_ordre_id) REFERENCES donneur_ordre (id)');
        $this->addSql('ALTER TABLE corres_colu ADD CONSTRAINT FK_DF3952496635C9C2 FOREIGN KEY (id_model_import_id) REFERENCES model_import (id)');
        $this->addSql('ALTER TABLE critere_model_facturation ADD CONSTRAINT FK_EE4ECA5EDEC978EE FOREIGN KEY (id_regle_id) REFERENCES regle_model_facturation (id)');
        $this->addSql('ALTER TABLE detail_competence ADD CONSTRAINT FK_2FCED8A9AB5ECCCE FOREIGN KEY (id_competence_id) REFERENCES competence (id)');
        $this->addSql('ALTER TABLE detail_competence ADD CONSTRAINT FK_2FCED8A969C24F6 FOREIGN KEY (id_param_id) REFERENCES param_activite (id)');
        $this->addSql('ALTER TABLE detail_model_affichage ADD CONSTRAINT FK_36F7E8CCEDF52E15 FOREIGN KEY (id_model_affichage_id) REFERENCES model_affichage (id)');
        $this->addSql('ALTER TABLE etap_activite ADD CONSTRAINT FK_2C4B5DFF831D4546 FOREIGN KEY (id_activite_id) REFERENCES activite (id)');
        $this->addSql('ALTER TABLE etap_activite ADD CONSTRAINT FK_2C4B5DFF69C24F6 FOREIGN KEY (id_param_id) REFERENCES param_activite (id)');
        $this->addSql('ALTER TABLE group_profil ADD CONSTRAINT FK_35506A47A76B6C5F FOREIGN KEY (id_profil_id) REFERENCES profil (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_profil ADD CONSTRAINT FK_35506A47AE8F35D2 FOREIGN KEY (id_group_id) REFERENCES groupe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE import ADD CONSTRAINT FK_9D4ECE1D29AE5B72 FOREIGN KEY (id_model_id) REFERENCES model_import (id)');
        $this->addSql('ALTER TABLE import ADD CONSTRAINT FK_9D4ECE1DA3E460EC FOREIGN KEY (id_integration_id) REFERENCES integration (id)');
        $this->addSql('ALTER TABLE import_donneur_ordre_back ADD CONSTRAINT FK_5D8AEDD73697DFFE FOREIGN KEY (id_import_id) REFERENCES import (id)');
        $this->addSql('ALTER TABLE interm_param_etap ADD CONSTRAINT FK_C21DE3385CA1B8D8 FOREIGN KEY (id_etap_id) REFERENCES etap_activite (id)');
        $this->addSql('ALTER TABLE interm_param_etap ADD CONSTRAINT FK_C21DE33883F508AD FOREIGN KEY (id_param_activite_id) REFERENCES param_activite (id)');
        $this->addSql('ALTER TABLE interm_param_sous_etap ADD CONSTRAINT FK_5C2507EB82F41C97 FOREIGN KEY (id_sous_etap_id) REFERENCES sous_etap_activite (id)');
        $this->addSql('ALTER TABLE interm_param_sous_etap ADD CONSTRAINT FK_5C2507EB69C24F6 FOREIGN KEY (id_param_id) REFERENCES param_activite (id)');
        $this->addSql('ALTER TABLE interm_resultat_activite ADD CONSTRAINT FK_87F4495B831D4546 FOREIGN KEY (id_activite_id) REFERENCES activite (id)');
        $this->addSql('ALTER TABLE interm_resultat_activite ADD CONSTRAINT FK_87F4495BCA2124AB FOREIGN KEY (id_resultat_id) REFERENCES resultat_activite (id)');
        $this->addSql('ALTER TABLE param_activite ADD CONSTRAINT FK_E53F14D138551A49 FOREIGN KEY (id_branche_id) REFERENCES type_parametrage (id)');
        $this->addSql('ALTER TABLE portefeuille ADD CONSTRAINT FK_2955FFFECBD6E4BE FOREIGN KEY (id_model_fact_id) REFERENCES model_facturation (id)');
        $this->addSql('ALTER TABLE portefeuille ADD CONSTRAINT FK_2955FFFED0C4FDF3 FOREIGN KEY (id_donneur_ordre_id) REFERENCES donneur_ordre (id)');
        $this->addSql('ALTER TABLE regle_model_facturation ADD CONSTRAINT FK_E19E07F729AE5B72 FOREIGN KEY (id_model_id) REFERENCES model_facturation (id)');
        $this->addSql('ALTER TABLE resultat_activite ADD CONSTRAINT FK_EED3238A69C24F6 FOREIGN KEY (id_param_id) REFERENCES param_activite (id)');
        $this->addSql('ALTER TABLE resultat_activite ADD CONSTRAINT FK_EED3238A831D4546 FOREIGN KEY (id_activite_id) REFERENCES activite (id)');
        $this->addSql('ALTER TABLE roles ADD CONSTRAINT FK_B63E2EC789E8BDC FOREIGN KEY (id_role_id) REFERENCES listes_roles (id)');
        $this->addSql('ALTER TABLE roles ADD CONSTRAINT FK_B63E2EC7A76B6C5F FOREIGN KEY (id_profil_id) REFERENCES profil (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sous_etap_activite ADD CONSTRAINT FK_112C58705CA1B8D8 FOREIGN KEY (id_etap_id) REFERENCES etap_activite (id)');
        $this->addSql('ALTER TABLE utilisateurs ADD CONSTRAINT FK_497B315EAE8F35D2 FOREIGN KEY (id_group_id) REFERENCES groupe (id) ON DELETE CASCADE');
    }
}
