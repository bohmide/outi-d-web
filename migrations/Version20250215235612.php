<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250215235612 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE parents ADD emailp VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE prof ADD emailpr VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE student ADD email VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE parents DROP emailp');
        $this->addSql('ALTER TABLE prof DROP emailpr');
        $this->addSql('ALTER TABLE student DROP email');
    }
}
