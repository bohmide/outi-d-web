<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250307023850 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE badge (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, required_score INT NOT NULL, icon VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE memory_card (id INT AUTO_INCREMENT NOT NULL, game_id INT DEFAULT NULL, images JSON NOT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_B927F3DDE48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_badges (user_id INT NOT NULL, badge_id INT NOT NULL, INDEX IDX_7711DD0AA76ED395 (user_id), INDEX IDX_7711DD0AF7A2C2FC (badge_id), PRIMARY KEY(user_id, badge_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE memory_card ADD CONSTRAINT FK_B927F3DDE48FD905 FOREIGN KEY (game_id) REFERENCES games (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE users_badges ADD CONSTRAINT FK_7711DD0AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE users_badges ADD CONSTRAINT FK_7711DD0AF7A2C2FC FOREIGN KEY (badge_id) REFERENCES badge (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE competition_equipe DROP FOREIGN KEY FK_4B0A7AC66D861B89');
        $this->addSql('ALTER TABLE competition_equipe DROP FOREIGN KEY FK_4B0A7AC67B39D312');
        $this->addSql('DROP TABLE competition_equipe');
        $this->addSql('DROP TABLE equipe');
        $this->addSql('ALTER TABLE certification ADD pdf_filename VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE competition DROP localisation, CHANGE fichier fichier VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE organisation ADD mot_de_passe VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX UNIQ_6D28840D5F37A13B ON payment');
        $this->addSql('ALTER TABLE payment ADD stripe_payment_id VARCHAR(50) DEFAULT NULL, DROP token, CHANGE order_id order_id VARCHAR(50) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6D28840D7FCD0533 ON payment (stripe_payment_id)');
        $this->addSql('ALTER TABLE quiz_kids ADD country VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE competition_equipe (equipe_id INT NOT NULL, competition_id INT NOT NULL, INDEX IDX_4B0A7AC66D861B89 (equipe_id), INDEX IDX_4B0A7AC67B39D312 (competition_id), PRIMARY KEY(equipe_id, competition_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE equipe (id INT AUTO_INCREMENT NOT NULL, nom_equipe VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ambassadeur VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, membres JSON NOT NULL COMMENT \'(DC2Type:json)\', travail VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE competition_equipe ADD CONSTRAINT FK_4B0A7AC66D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE competition_equipe ADD CONSTRAINT FK_4B0A7AC67B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE memory_card DROP FOREIGN KEY FK_B927F3DDE48FD905');
        $this->addSql('ALTER TABLE users_badges DROP FOREIGN KEY FK_7711DD0AA76ED395');
        $this->addSql('ALTER TABLE users_badges DROP FOREIGN KEY FK_7711DD0AF7A2C2FC');
        $this->addSql('DROP TABLE badge');
        $this->addSql('DROP TABLE memory_card');
        $this->addSql('DROP TABLE users_badges');
        $this->addSql('ALTER TABLE certification DROP pdf_filename');
        $this->addSql('ALTER TABLE competition ADD localisation VARCHAR(255) DEFAULT NULL, CHANGE fichier fichier VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE organisation DROP mot_de_passe');
        $this->addSql('DROP INDEX UNIQ_6D28840D7FCD0533 ON payment');
        $this->addSql('ALTER TABLE payment ADD token VARCHAR(255) NOT NULL, DROP stripe_payment_id, CHANGE order_id order_id VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6D28840D5F37A13B ON payment (token)');
        $this->addSql('ALTER TABLE quiz_kids DROP country');
    }
}
