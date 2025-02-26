<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250226091444 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE admin (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE certification (id INT AUTO_INCREMENT NOT NULL, nom_certification VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE challenge (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, progress VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chapitre (id INT AUTO_INCREMENT NOT NULL, cours_id INT NOT NULL, quiz_id INT DEFAULT NULL, nom_chapitre VARCHAR(100) NOT NULL, contenu VARCHAR(255) DEFAULT NULL, contenu_text LONGTEXT DEFAULT NULL, INDEX IDX_8C62B0257ECF78B0 (cours_id), UNIQUE INDEX UNIQ_8C62B025853CD175 (quiz_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, description VARCHAR(255) NOT NULL, date_creation DATE NOT NULL, INDEX IDX_9474526C4B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competition (id INT AUTO_INCREMENT NOT NULL, nom_comp VARCHAR(255) NOT NULL, nom_entreprise VARCHAR(255) NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, description VARCHAR(255) NOT NULL, fichier VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cours (id INT AUTO_INCREMENT NOT NULL, certification_id INT DEFAULT NULL, nom VARCHAR(100) NOT NULL, date_creation DATE NOT NULL, etat VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_FDCA8C9CCB47068A (certification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evenements (id INT AUTO_INCREMENT NOT NULL, genre_id INT NOT NULL, nom_event VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, date_event DATE NOT NULL, nbr_members INT DEFAULT NULL, image_path VARCHAR(255) DEFAULT NULL, INDEX IDX_E10AD4004296D31F (genre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evenements_sponsors (evenements_id INT NOT NULL, sponsors_id INT NOT NULL, INDEX IDX_4FCEFFE063C02CD4 (evenements_id), INDEX IDX_4FCEFFE0FB0F2BBC (sponsors_id), PRIMARY KEY(evenements_id, sponsors_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_genre (id INT AUTO_INCREMENT NOT NULL, nom_genre VARCHAR(255) NOT NULL, nbr INT DEFAULT NULL, image_path VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_C24B82D429A8AF6D (nom_genre), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE forum (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, theme VARCHAR(255) NOT NULL, date_creation DATE NOT NULL, image_forum VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parents (id INT NOT NULL, first_name_child VARCHAR(255) NOT NULL, last_name_child VARCHAR(255) NOT NULL, birthday_child DATE NOT NULL, sexe_child VARCHAR(255) NOT NULL, learning_difficulties_child VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, forum_id INT NOT NULL, nom VARCHAR(255) NOT NULL, date_creation DATE NOT NULL, nb_like INT DEFAULT NULL, nb_comment INT DEFAULT NULL, contenu VARCHAR(255) NOT NULL, INDEX IDX_5A8A6C8D29CCBAD0 (forum_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prof (id INT NOT NULL, interdate DATE NOT NULL, intermode VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, quiz_id INT NOT NULL, question VARCHAR(255) NOT NULL, type VARCHAR(100) DEFAULT NULL, INDEX IDX_B6F7494E853CD175 (quiz_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quiz (id INT AUTO_INCREMENT NOT NULL, score INT DEFAULT NULL, titre VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quiz_kids (id INT AUTO_INCREMENT NOT NULL, id_challenge_id INT DEFAULT NULL, question VARCHAR(255) NOT NULL, options JSON NOT NULL COMMENT \'(DC2Type:json)\', correct_answer VARCHAR(255) NOT NULL, media VARCHAR(255) DEFAULT NULL, level VARCHAR(255) NOT NULL, score VARCHAR(255) NOT NULL, updated_at DATETIME DEFAULT NULL, genre VARCHAR(255) NOT NULL, INDEX IDX_88092DCFBB636FB4 (id_challenge_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponse (id INT AUTO_INCREMENT NOT NULL, question_id INT NOT NULL, reponse VARCHAR(255) NOT NULL, is_correct TINYINT(1) NOT NULL, INDEX IDX_5FB6DEC71E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sponsors (id INT AUTO_INCREMENT NOT NULL, nom_sponsor VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, image_path VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student (id INT NOT NULL, datebirth DATE NOT NULL, sexe VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE admin ADD CONSTRAINT FK_880E0D76BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chapitre ADD CONSTRAINT FK_8C62B0257ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('ALTER TABLE chapitre ADD CONSTRAINT FK_8C62B025853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C4B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9CCB47068A FOREIGN KEY (certification_id) REFERENCES certification (id)');
        $this->addSql('ALTER TABLE evenements ADD CONSTRAINT FK_E10AD4004296D31F FOREIGN KEY (genre_id) REFERENCES event_genre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evenements_sponsors ADD CONSTRAINT FK_4FCEFFE063C02CD4 FOREIGN KEY (evenements_id) REFERENCES evenements (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evenements_sponsors ADD CONSTRAINT FK_4FCEFFE0FB0F2BBC FOREIGN KEY (sponsors_id) REFERENCES sponsors (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE parents ADD CONSTRAINT FK_FD501D6ABF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D29CCBAD0 FOREIGN KEY (forum_id) REFERENCES forum (id)');
        $this->addSql('ALTER TABLE prof ADD CONSTRAINT FK_5BBA70BBBF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)');
        $this->addSql('ALTER TABLE quiz_kids ADD CONSTRAINT FK_88092DCFBB636FB4 FOREIGN KEY (id_challenge_id) REFERENCES challenge (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC71E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admin DROP FOREIGN KEY FK_880E0D76BF396750');
        $this->addSql('ALTER TABLE chapitre DROP FOREIGN KEY FK_8C62B0257ECF78B0');
        $this->addSql('ALTER TABLE chapitre DROP FOREIGN KEY FK_8C62B025853CD175');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C4B89032C');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9CCB47068A');
        $this->addSql('ALTER TABLE evenements DROP FOREIGN KEY FK_E10AD4004296D31F');
        $this->addSql('ALTER TABLE evenements_sponsors DROP FOREIGN KEY FK_4FCEFFE063C02CD4');
        $this->addSql('ALTER TABLE evenements_sponsors DROP FOREIGN KEY FK_4FCEFFE0FB0F2BBC');
        $this->addSql('ALTER TABLE parents DROP FOREIGN KEY FK_FD501D6ABF396750');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D29CCBAD0');
        $this->addSql('ALTER TABLE prof DROP FOREIGN KEY FK_5BBA70BBBF396750');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E853CD175');
        $this->addSql('ALTER TABLE quiz_kids DROP FOREIGN KEY FK_88092DCFBB636FB4');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC71E27F6BF');
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF33BF396750');
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE certification');
        $this->addSql('DROP TABLE challenge');
        $this->addSql('DROP TABLE chapitre');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE competition');
        $this->addSql('DROP TABLE cours');
        $this->addSql('DROP TABLE evenements');
        $this->addSql('DROP TABLE evenements_sponsors');
        $this->addSql('DROP TABLE event_genre');
        $this->addSql('DROP TABLE forum');
        $this->addSql('DROP TABLE parents');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE prof');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE quiz');
        $this->addSql('DROP TABLE quiz_kids');
        $this->addSql('DROP TABLE reponse');
        $this->addSql('DROP TABLE sponsors');
        $this->addSql('DROP TABLE student');
        $this->addSql('DROP TABLE user');
    }
}
