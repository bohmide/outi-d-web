<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250306215608 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_6D28840D7FCD0533 ON payment');
        $this->addSql('ALTER TABLE payment ADD token VARCHAR(255) DEFAULT NULL, ADD stripe_charge_id VARCHAR(255) DEFAULT NULL, DROP stripe_payment_id, CHANGE order_id order_id VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6D28840D5F37A13B ON payment (token)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_6D28840D5F37A13B ON payment');
        $this->addSql('ALTER TABLE payment ADD stripe_payment_id VARCHAR(50) DEFAULT NULL, DROP token, DROP stripe_charge_id, CHANGE order_id order_id VARCHAR(50) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6D28840D7FCD0533 ON payment (stripe_payment_id)');
    }
}
