<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210719071811 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
       $this->addSql('ALTER TABLE piecesjointes ADD commentaire VARCHAR(255) DEFAULT NULL, CHANGE etat etat VARCHAR(50) DEFAULT NULL');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agence CHANGE gerant_id gerant_id INT DEFAULT NULL, CHANGE postal_code postal_code VARCHAR(15) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE statut statut VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE demande demande VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE ville_agence ville_agence VARCHAR(30) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE telephone_agence telephone_agence VARCHAR(10) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE siret siret VARCHAR(14) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE num_agrement num_agrement VARCHAR(15) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE num_declar_activite num_declar_activite VARCHAR(15) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE nom_assurance nom_assurance VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE num_police_assurance num_police_assurance VARCHAR(15) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE nom_fd_garantie nom_fd_garantie VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE num_fd_garantie num_fd_garantie VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE montant_fd_garantie montant_fd_garantie DOUBLE PRECISION DEFAULT \'NULL\', CHANGE nom_interloc nom_interloc VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE prenom_interloc prenom_interloc VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE tel_interloc tel_interloc VARCHAR(10) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE mail_interloc mail_interloc VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE lieu_agence lieu_agence VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE appointment CHANGE motif_id motif_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commande CHANGE date_commande date_commande DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE eleve DROP FOREIGN KEY FK_ECA105F7B55A6279');
        $this->addSql('ALTER TABLE eleve DROP FOREIGN KEY FK_ECA105F7906D5F2C');
        $this->addSql('ALTER TABLE eleve DROP FOREIGN KEY FK_ECA105F7F9B9FED6');
        $this->addSql('ALTER TABLE eleve DROP FOREIGN KEY FK_ECA105F7BF396750');
        $this->addSql('DROP INDEX IDX_ECA105F7F9B9FED6 ON eleve');
        $this->addSql('ALTER TABLE eleve CHANGE agence_id agence_id INT DEFAULT NULL, CHANGE short_list_id short_list_id INT DEFAULT NULL, CHANGE lieu_id lieu_id INT DEFAULT NULL, CHANGE soumis_par_id soumis_par_id INT DEFAULT NULL, CHANGE eval_pre_id eval_pre_id INT DEFAULT NULL, CHANGE forfait_id forfait_id INT DEFAULT NULL, CHANGE porte_ouverte_id porte_ouverte_id INT DEFAULT NULL, CHANGE autre_prenoms autre_prenoms VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE nom_usage nom_usage VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE date_naiss date_naiss DATE DEFAULT \'NULL\', CHANGE tel_parent tel_parent VARCHAR(10) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE mail_parent mail_parent VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE adresse adresse VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE ville ville VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE cp cp VARCHAR(5) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE pays_naiss pays_naiss VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE dep_naiss dep_naiss VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE ville_naiss ville_naiss VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE lunette lunette VARCHAR(3) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE statut_social statut_social VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE lycee lycee VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE lycee_autre lycee_autre VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE nom_societe nom_societe VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE metier metier VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE heure_prevue heure_prevue NUMERIC(5, 2) DEFAULT \'NULL\', CHANGE examen_reussi examen_reussi TINYINT(1) DEFAULT \'NULL\', CHANGE contrat_signe contrat_signe VARCHAR(1) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE etat_dossier etat_dossier VARCHAR(1) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE code code VARCHAR(1) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE formulaire_inscription formulaire_inscription VARCHAR(1) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE neph neph VARCHAR(12) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE commentaire_pj commentaire_pj VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE nb_personne_porte_ouverte nb_personne_porte_ouverte INT DEFAULT NULL, CHANGE porte_ouverte_annule porte_ouverte_annule VARCHAR(1) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE present_journee_info present_journee_info VARCHAR(1) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE eval_pre DROP FOREIGN KEY FK_AB52B13A6CC7B2');
        $this->addSql('ALTER TABLE eval_pre CHANGE eleve_id eleve_id INT DEFAULT NULL, CHANGE permis permis LONGTEXT CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:simple_array)\', CHANGE ou ou LONGTEXT CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:simple_array)\', CHANGE score_code score_code VARCHAR(2) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE score_conduite score_conduite VARCHAR(2) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE date_evaluation date_evaluation DATE DEFAULT \'NULL\', CHANGE nb_heure_theorique nb_heure_theorique VARCHAR(3) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE nb_heure_pratique nb_heure_pratique VARCHAR(6) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE examen DROP FOREIGN KEY FK_514C8FEC6AB213CC');
        $this->addSql('ALTER TABLE examen DROP FOREIGN KEY FK_514C8FECA234A5D3');
        $this->addSql('ALTER TABLE forfait ADD code VARCHAR(40) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP contenu_forfait');
        $this->addSql('ALTER TABLE forfait_agence DROP FOREIGN KEY FK_1A5694AC906D5F2C');
        $this->addSql('ALTER TABLE forfait_agence DROP FOREIGN KEY FK_1A5694ACD725330D');
        $this->addSql('ALTER TABLE gerant DROP FOREIGN KEY FK_D1D45C70BF396750');
        $this->addSql('ALTER TABLE indispo_type DROP FOREIGN KEY FK_C48C95C5A234A5D3');
        $this->addSql('ALTER TABLE indisponibilite DROP FOREIGN KEY FK_8717036FA234A5D3');
        $this->addSql('ALTER TABLE indisponibilite CHANGE pre_exam pre_exam TINYINT(1) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE lieu DROP FOREIGN KEY FK_2F577D59A234A5D3');
        $this->addSql('ALTER TABLE lieu CHANGE moniteur_id moniteur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lycee CHANGE adresse adresse VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE moniteur DROP FOREIGN KEY FK_B3EC8EBAD725330D');
        $this->addSql('ALTER TABLE moniteur DROP FOREIGN KEY FK_B3EC8EBABF396750');
        $this->addSql('ALTER TABLE moniteur CHANGE agence_id agence_id INT DEFAULT NULL, CHANGE color color VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF2A6CC7B2');
        $this->addSql('ALTER TABLE piecesjointes DROP commentaire, CHANGE etat etat VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE place_examen DROP FOREIGN KEY FK_F3828885A234A5D3');
        $this->addSql('ALTER TABLE place_examen DROP FOREIGN KEY FK_F38288856AB213CC');
        $this->addSql('ALTER TABLE place_examen CHANGE nb_place_attribuee nb_place_attribuee INT DEFAULT NULL, CHANGE nb_place_demande nb_place_demande INT DEFAULT NULL, CHANGE nb_place_utilise nb_place_utilise INT DEFAULT NULL');
        $this->addSql('ALTER TABLE prestation CHANGE detail detail VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE secretaire DROP FOREIGN KEY FK_7DB5C2D0D725330D');
        $this->addSql('ALTER TABLE secretaire DROP FOREIGN KEY FK_7DB5C2D0BF396750');
        $this->addSql('DROP INDEX IDX_7DB5C2D0D725330D ON secretaire');
        $this->addSql('ALTER TABLE short_list DROP FOREIGN KEY FK_240F368DF8090AEE');
        $this->addSql('ALTER TABLE short_list CHANGE moniteurs_id moniteurs_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE utilisateur CHANGE telephone telephone VARCHAR(10) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }
}
