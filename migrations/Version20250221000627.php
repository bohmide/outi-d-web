<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250221000627 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, description VARCHAR(255) NOT NULL, date_creation DATE NOT NULL, INDEX IDX_9474526C4B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evenements_sponsors (evenements_id INT NOT NULL, sponsors_id INT NOT NULL, INDEX IDX_4FCEFFE063C02CD4 (evenements_id), INDEX IDX_4FCEFFE0FB0F2BBC (sponsors_id), PRIMARY KEY(evenements_id, sponsors_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parents (id INT AUTO_INCREMENT NOT NULL, fnp VARCHAR(255) NOT NULL, lnp VARCHAR(255) NOT NULL, pwp VARCHAR(255) NOT NULL, pvp VARCHAR(255) NOT NULL, fnch VARCHAR(255) NOT NULL, lnch VARCHAR(255) NOT NULL, dbch DATE NOT NULL, sch VARCHAR(255) NOT NULL, ldch VARCHAR(255) NOT NULL, emailp VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, forum_id INT NOT NULL, nom VARCHAR(255) NOT NULL, date_creation DATE NOT NULL, nb_like INT DEFAULT NULL, nb_comment INT DEFAULT NULL, contenu VARCHAR(255) NOT NULL, INDEX IDX_5A8A6C8D29CCBAD0 (forum_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prof (id INT AUTO_INCREMENT NOT NULL, fnpr VARCHAR(255) NOT NULL, lnpr VARCHAR(255) NOT NULL, pwpr VARCHAR(255) NOT NULL, pvpr VARCHAR(255) NOT NULL, interdate DATE NOT NULL, intermode VARCHAR(255) NOT NULL, emailpr VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student (id INT AUTO_INCREMENT NOT NULL, fn VARCHAR(255) NOT NULL, ln VARCHAR(255) NOT NULL, pw VARCHAR(255) NOT NULL, pv VARCHAR(255) NOT NULL, datebirth DATE NOT NULL, sexe VARCHAR(255) NOT NULL, ld VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C4B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE evenements_sponsors ADD CONSTRAINT FK_4FCEFFE063C02CD4 FOREIGN KEY (evenements_id) REFERENCES evenements (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evenements_sponsors ADD CONSTRAINT FK_4FCEFFE0FB0F2BBC FOREIGN KEY (sponsors_id) REFERENCES sponsors (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D29CCBAD0 FOREIGN KEY (forum_id) REFERENCES forum (id)');
        $this->addSql('ALTER TABLE chapitre DROP FOREIGN KEY FK_8C62B025853CD175');
        $this->addSql('ALTER TABLE chapitre ADD contenu_text LONGTEXT DEFAULT NULL, CHANGE contenu contenu VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE chapitre ADD CONSTRAINT FK_8C62B025853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE evenements ADD genre_id INT NOT NULL');
        $this->addSql('ALTER TABLE evenements ADD CONSTRAINT FK_E10AD4004296D31F FOREIGN KEY (genre_id) REFERENCES event_genre (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_E10AD4004296D31F ON evenements (genre_id)');
        $this->addSql('ALTER TABLE quiz ADD titre VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE quiz_kids ADD genre VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C4B89032C');
        $this->addSql('ALTER TABLE evenements_sponsors DROP FOREIGN KEY FK_4FCEFFE063C02CD4');
        $this->addSql('ALTER TABLE evenements_sponsors DROP FOREIGN KEY FK_4FCEFFE0FB0F2BBC');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D29CCBAD0');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE evenements_sponsors');
        $this->addSql('DROP TABLE parents');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE prof');
        $this->addSql('DROP TABLE student');
        $this->addSql('ALTER TABLE chapitre DROP FOREIGN KEY FK_8C62B025853CD175');
        $this->addSql('ALTER TABLE chapitre DROP contenu_text, CHANGE contenu contenu VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE chapitre ADD CONSTRAINT FK_8C62B025853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)');
        $this->addSql('ALTER TABLE evenements DROP FOREIGN KEY FK_E10AD4004296D31F');
        $this->addSql('DROP INDEX IDX_E10AD4004296D31F ON evenements');
        $this->addSql('ALTER TABLE evenements DROP genre_id');
        $this->addSql('ALTER TABLE quiz DROP titre');
        $this->addSql('ALTER TABLE quiz_kids DROP genre');
    }
}
