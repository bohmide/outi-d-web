<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250303230425 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE challenge (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, progress VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE games (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE puzzle (id INT AUTO_INCREMENT NOT NULL, game_id INT DEFAULT NULL, final_image VARCHAR(255) DEFAULT NULL, pieces JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_22A6DFDFE48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE puzzle ADD CONSTRAINT FK_22A6DFDFE48FD905 FOREIGN KEY (game_id) REFERENCES games (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE admin ADD CONSTRAINT FK_880E0D76BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE competition ADD localisation VARCHAR(255) DEFAULT NULL, DROP updated_at, CHANGE fichier fichier VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE evenements ADD date_creation DATE NOT NULL');
        $this->addSql('ALTER TABLE event_genre ADD date_creation DATE NOT NULL');
        $this->addSql('ALTER TABLE parents ADD first_name_child VARCHAR(255) NOT NULL, ADD last_name_child VARCHAR(255) NOT NULL, ADD sexe_child VARCHAR(255) NOT NULL, ADD learning_difficulties_child VARCHAR(255) NOT NULL, DROP fnp, DROP lnp, DROP pwp, DROP pvp, DROP fnch, DROP lnch, DROP sch, DROP ldch, DROP emailp, CHANGE id id INT NOT NULL, CHANGE dbch birthday_child DATE NOT NULL');
        $this->addSql('ALTER TABLE parents ADD CONSTRAINT FK_FD501D6ABF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE prof DROP fnpr, DROP lnpr, DROP pwpr, DROP pvpr, DROP emailpr, CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE prof ADD CONSTRAINT FK_5BBA70BBBF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE question CHANGE type type VARCHAR(20) NOT NULL');
        $this->addSql('ALTER TABLE quiz_kids ADD challenge_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz_kids ADD CONSTRAINT FK_88092DCF98A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenge (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_88092DCF98A21AC6 ON quiz_kids (challenge_id)');
        $this->addSql('ALTER TABLE sponsors ADD date_creation DATE NOT NULL');
        $this->addSql('ALTER TABLE student DROP fn, DROP ln, DROP pw, DROP pv, DROP ld, DROP email, CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quiz_kids DROP FOREIGN KEY FK_88092DCF98A21AC6');
        $this->addSql('ALTER TABLE admin DROP FOREIGN KEY FK_880E0D76BF396750');
        $this->addSql('ALTER TABLE parents DROP FOREIGN KEY FK_FD501D6ABF396750');
        $this->addSql('ALTER TABLE prof DROP FOREIGN KEY FK_5BBA70BBBF396750');
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF33BF396750');
        $this->addSql('ALTER TABLE puzzle DROP FOREIGN KEY FK_22A6DFDFE48FD905');
        $this->addSql('DROP TABLE challenge');
        $this->addSql('DROP TABLE games');
        $this->addSql('DROP TABLE puzzle');
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE competition ADD updated_at DATETIME DEFAULT NULL, DROP localisation, CHANGE fichier fichier VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE evenements DROP date_creation');
        $this->addSql('ALTER TABLE event_genre DROP date_creation');
        $this->addSql('ALTER TABLE parents ADD fnp VARCHAR(255) NOT NULL, ADD lnp VARCHAR(255) NOT NULL, ADD pwp VARCHAR(255) NOT NULL, ADD pvp VARCHAR(255) NOT NULL, ADD fnch VARCHAR(255) NOT NULL, ADD lnch VARCHAR(255) NOT NULL, ADD sch VARCHAR(255) NOT NULL, ADD ldch VARCHAR(255) NOT NULL, ADD emailp VARCHAR(255) NOT NULL, DROP first_name_child, DROP last_name_child, DROP sexe_child, DROP learning_difficulties_child, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE birthday_child dbch DATE NOT NULL');
        $this->addSql('ALTER TABLE prof ADD fnpr VARCHAR(255) NOT NULL, ADD lnpr VARCHAR(255) NOT NULL, ADD pwpr VARCHAR(255) NOT NULL, ADD pvpr VARCHAR(255) NOT NULL, ADD emailpr VARCHAR(255) NOT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE question CHANGE type type VARCHAR(100) DEFAULT NULL');
        $this->addSql('DROP INDEX IDX_88092DCF98A21AC6 ON quiz_kids');
        $this->addSql('ALTER TABLE quiz_kids DROP challenge_id');
        $this->addSql('ALTER TABLE sponsors DROP date_creation');
        $this->addSql('ALTER TABLE student ADD fn VARCHAR(255) NOT NULL, ADD ln VARCHAR(255) NOT NULL, ADD pw VARCHAR(255) NOT NULL, ADD pv VARCHAR(255) NOT NULL, ADD ld VARCHAR(255) NOT NULL, ADD email VARCHAR(255) NOT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL');
    }
}
