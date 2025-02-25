<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250225192439 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE admin (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE admin ADD CONSTRAINT FK_880E0D76BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE parents ADD first_name_child VARCHAR(255) NOT NULL, ADD last_name_child VARCHAR(255) NOT NULL, ADD sexe_child VARCHAR(255) NOT NULL, ADD learning_difficulties_child VARCHAR(255) NOT NULL, DROP fnp, DROP lnp, DROP pwp, DROP pvp, DROP fnch, DROP lnch, DROP sch, DROP ldch, DROP emailp, CHANGE id id INT NOT NULL, CHANGE dbch birthday_child DATE NOT NULL');
        $this->addSql('ALTER TABLE parents ADD CONSTRAINT FK_FD501D6ABF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE prof DROP fnpr, DROP lnpr, DROP pwpr, DROP pvpr, DROP emailpr, CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE prof ADD CONSTRAINT FK_5BBA70BBBF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student ADD learning_difficulties_student VARCHAR(255) NOT NULL, DROP fn, DROP ln, DROP pw, DROP pv, DROP ld, DROP email, CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD password_verif VARCHAR(255) NOT NULL, ADD roles JSON NOT NULL COMMENT \'(DC2Type:json)\', ADD type VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admin DROP FOREIGN KEY FK_880E0D76BF396750');
        $this->addSql('DROP TABLE admin');
        $this->addSql('ALTER TABLE parents DROP FOREIGN KEY FK_FD501D6ABF396750');
        $this->addSql('ALTER TABLE parents ADD fnp VARCHAR(255) NOT NULL, ADD lnp VARCHAR(255) NOT NULL, ADD pwp VARCHAR(255) NOT NULL, ADD pvp VARCHAR(255) NOT NULL, ADD fnch VARCHAR(255) NOT NULL, ADD lnch VARCHAR(255) NOT NULL, ADD sch VARCHAR(255) NOT NULL, ADD ldch VARCHAR(255) NOT NULL, ADD emailp VARCHAR(255) NOT NULL, DROP first_name_child, DROP last_name_child, DROP sexe_child, DROP learning_difficulties_child, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE birthday_child dbch DATE NOT NULL');
        $this->addSql('ALTER TABLE prof DROP FOREIGN KEY FK_5BBA70BBBF396750');
        $this->addSql('ALTER TABLE prof ADD fnpr VARCHAR(255) NOT NULL, ADD lnpr VARCHAR(255) NOT NULL, ADD pwpr VARCHAR(255) NOT NULL, ADD pvpr VARCHAR(255) NOT NULL, ADD emailpr VARCHAR(255) NOT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF33BF396750');
        $this->addSql('ALTER TABLE student ADD ln VARCHAR(255) NOT NULL, ADD pw VARCHAR(255) NOT NULL, ADD pv VARCHAR(255) NOT NULL, ADD ld VARCHAR(255) NOT NULL, ADD email VARCHAR(255) NOT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE learning_difficulties_student fn VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user DROP password_verif, DROP roles, DROP type');
    }
}
