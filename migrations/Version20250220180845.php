<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250220180845 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE organisation (id INT AUTO_INCREMENT NOT NULL, nom_organisation VARCHAR(255) NOT NULL, domaine VARCHAR(255) DEFAULT NULL, mot_de_passe VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chapitre DROP FOREIGN KEY FK_8C62B025853CD175');
        $this->addSql('ALTER TABLE chapitre ADD contenu_text LONGTEXT DEFAULT NULL, CHANGE contenu contenu VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE chapitre ADD CONSTRAINT FK_8C62B025853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE competition ADD organisation_id INT NOT NULL');
        $this->addSql('ALTER TABLE competition ADD CONSTRAINT FK_B50A2CB19E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id)');
        $this->addSql('CREATE INDEX IDX_B50A2CB19E6B1585 ON competition (organisation_id)');
        $this->addSql('ALTER TABLE quiz ADD titre VARCHAR(100) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE competition DROP FOREIGN KEY FK_B50A2CB19E6B1585');
        $this->addSql('DROP TABLE organisation');
        $this->addSql('ALTER TABLE chapitre DROP FOREIGN KEY FK_8C62B025853CD175');
        $this->addSql('ALTER TABLE chapitre DROP contenu_text, CHANGE contenu contenu VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE chapitre ADD CONSTRAINT FK_8C62B025853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)');
        $this->addSql('DROP INDEX IDX_B50A2CB19E6B1585 ON competition');
        $this->addSql('ALTER TABLE competition DROP organisation_id');
        $this->addSql('ALTER TABLE quiz DROP titre');
    }
}
