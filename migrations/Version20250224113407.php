<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250224113407 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE challenge (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, progress VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chapitre DROP FOREIGN KEY FK_8C62B025853CD175');
        $this->addSql('ALTER TABLE chapitre ADD contenu_text LONGTEXT DEFAULT NULL, CHANGE contenu contenu VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE chapitre ADD CONSTRAINT FK_8C62B025853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE quiz ADD titre VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE quiz_kids ADD id_challenge_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz_kids ADD CONSTRAINT FK_88092DCFBB636FB4 FOREIGN KEY (id_challenge_id) REFERENCES challenge (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_88092DCFBB636FB4 ON quiz_kids (id_challenge_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quiz_kids DROP FOREIGN KEY FK_88092DCFBB636FB4');
        $this->addSql('DROP TABLE challenge');
        $this->addSql('ALTER TABLE chapitre DROP FOREIGN KEY FK_8C62B025853CD175');
        $this->addSql('ALTER TABLE chapitre DROP contenu_text, CHANGE contenu contenu VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE chapitre ADD CONSTRAINT FK_8C62B025853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)');
        $this->addSql('ALTER TABLE quiz DROP titre');
        $this->addSql('DROP INDEX IDX_88092DCFBB636FB4 ON quiz_kids');
        $this->addSql('ALTER TABLE quiz_kids DROP id_challenge_id');
    }
}
