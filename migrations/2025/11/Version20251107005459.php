<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251107005459 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cars (id UUID NOT NULL, agency_id UUID DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, registration_number VARCHAR(20) NOT NULL, brand VARCHAR(80) NOT NULL, model VARCHAR(80) NOT NULL, year INT NOT NULL, category VARCHAR(255) NOT NULL, fuel_type VARCHAR(255) NOT NULL, transmission VARCHAR(255) NOT NULL, color VARCHAR(30) DEFAULT NULL, mileage INT NOT NULL, daily_rental_price NUMERIC(10, 2) NOT NULL, status VARCHAR(255) NOT NULL, active BOOLEAN NOT NULL, is_under_maintenance BOOLEAN NOT NULL, last_service_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, next_service_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, insurance_expiration_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, acquisition_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, seats INT NOT NULL, doors INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_95C71D1438CEDFBE ON cars (registration_number)');
        $this->addSql('CREATE INDEX IDX_95C71D14CDEADB2A ON cars (agency_id)');
        $this->addSql('COMMENT ON COLUMN cars.id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN cars.agency_id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN cars.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN cars.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN cars.last_service_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN cars.next_service_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN cars.insurance_expiration_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN cars.acquisition_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE cars ADD CONSTRAINT FK_95C71D14CDEADB2A FOREIGN KEY (agency_id) REFERENCES agencies (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cars DROP CONSTRAINT FK_95C71D14CDEADB2A');
        $this->addSql('DROP TABLE cars');
    }
}
