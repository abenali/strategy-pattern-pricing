<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251127143612 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE customers (id VARCHAR(36) NOT NULL, email VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, total_purchases DOUBLE PRECISION NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_62534E21E7927C74 ON customers (email)');
        $this->addSql('CREATE TABLE products (id VARCHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE promotional_events (id VARCHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(100) NOT NULL, discount_percentage INT NOT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F519BA2E77153098 ON promotional_events (code)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE customers');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE promotional_events');
    }
}
