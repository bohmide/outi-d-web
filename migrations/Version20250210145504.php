<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250210145504 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE evenements_sponsors (evenements_id INT NOT NULL, sponsors_id INT NOT NULL, INDEX IDX_4FCEFFE063C02CD4 (evenements_id), INDEX IDX_4FCEFFE0FB0F2BBC (sponsors_id), PRIMARY KEY(evenements_id, sponsors_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE evenements_sponsors ADD CONSTRAINT FK_4FCEFFE063C02CD4 FOREIGN KEY (evenements_id) REFERENCES evenements (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evenements_sponsors ADD CONSTRAINT FK_4FCEFFE0FB0F2BBC FOREIGN KEY (sponsors_id) REFERENCES sponsors (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evenements ADD genre_id INT NOT NULL');
        $this->addSql('ALTER TABLE evenements ADD CONSTRAINT FK_E10AD4004296D31F FOREIGN KEY (genre_id) REFERENCES event_genre (id)');
        $this->addSql('CREATE INDEX IDX_E10AD4004296D31F ON evenements (genre_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenements_sponsors DROP FOREIGN KEY FK_4FCEFFE063C02CD4');
        $this->addSql('ALTER TABLE evenements_sponsors DROP FOREIGN KEY FK_4FCEFFE0FB0F2BBC');
        $this->addSql('DROP TABLE evenements_sponsors');
        $this->addSql('ALTER TABLE evenements DROP FOREIGN KEY FK_E10AD4004296D31F');
        $this->addSql('DROP INDEX IDX_E10AD4004296D31F ON evenements');
        $this->addSql('ALTER TABLE evenements DROP genre_id');
    }
}
