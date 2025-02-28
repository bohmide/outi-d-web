<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250227205007 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE puzzle DROP FOREIGN KEY FK_22A6DFDFE48FD905');
        $this->addSql('ALTER TABLE puzzle CHANGE game_id game_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE puzzle ADD CONSTRAINT FK_22A6DFDFE48FD905 FOREIGN KEY (game_id) REFERENCES games (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE puzzle DROP FOREIGN KEY FK_22A6DFDFE48FD905');
        $this->addSql('ALTER TABLE puzzle CHANGE game_id game_id INT NOT NULL');
        $this->addSql('ALTER TABLE puzzle ADD CONSTRAINT FK_22A6DFDFE48FD905 FOREIGN KEY (game_id) REFERENCES games (id)');
    }
}
