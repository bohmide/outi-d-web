<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250212100424 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenements DROP FOREIGN KEY FK_E10AD4004296D31F');
        $this->addSql('ALTER TABLE evenements ADD CONSTRAINT FK_E10AD4004296D31F FOREIGN KEY (genre_id) REFERENCES event_genre (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenements DROP FOREIGN KEY FK_E10AD4004296D31F');
        $this->addSql('ALTER TABLE evenements ADD CONSTRAINT FK_E10AD4004296D31F FOREIGN KEY (genre_id) REFERENCES event_genre (id)');
    }
}
