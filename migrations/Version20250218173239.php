<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250218173239 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE competition (id INT AUTO_INCREMENT NOT NULL, nom_comp VARCHAR(255) NOT NULL, nom_entreprise VARCHAR(255) NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, description VARCHAR(255) NOT NULL, fichier VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parents (id INT AUTO_INCREMENT NOT NULL, fnp VARCHAR(255) NOT NULL, lnp VARCHAR(255) NOT NULL, pwp VARCHAR(255) NOT NULL, pvp VARCHAR(255) NOT NULL, fnch VARCHAR(255) NOT NULL, lnch VARCHAR(255) NOT NULL, dbch DATE NOT NULL, sch VARCHAR(255) NOT NULL, ldch VARCHAR(255) NOT NULL, emailp VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prof (id INT AUTO_INCREMENT NOT NULL, fnpr VARCHAR(255) NOT NULL, lnpr VARCHAR(255) NOT NULL, pwpr VARCHAR(255) NOT NULL, pvpr VARCHAR(255) NOT NULL, interdate DATE NOT NULL, intermode VARCHAR(255) NOT NULL, emailpr VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quiz_kids (id INT AUTO_INCREMENT NOT NULL, question VARCHAR(255) NOT NULL, options JSON NOT NULL COMMENT \'(DC2Type:json)\', correct_answer VARCHAR(255) NOT NULL, media VARCHAR(255) DEFAULT NULL, level VARCHAR(255) NOT NULL, score VARCHAR(255) NOT NULL, updated_at DATETIME DEFAULT NULL, genre VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student (id INT AUTO_INCREMENT NOT NULL, fn VARCHAR(255) NOT NULL, ln VARCHAR(255) NOT NULL, pw VARCHAR(255) NOT NULL, pv VARCHAR(255) NOT NULL, datebirth DATE NOT NULL, sexe VARCHAR(255) NOT NULL, ld VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE competition');
        $this->addSql('DROP TABLE parents');
        $this->addSql('DROP TABLE prof');
        $this->addSql('DROP TABLE quiz_kids');
        $this->addSql('DROP TABLE student');
    }
}
