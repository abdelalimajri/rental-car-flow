<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251121232131 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customers ALTER license_expiration_date DROP NOT NULL');
        $this->addSql('ALTER TABLE customers ALTER birth_date DROP NOT NULL');
        $this->addSql('ALTER TABLE customers ALTER address DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customers ALTER license_expiration_date SET NOT NULL');
        $this->addSql('ALTER TABLE customers ALTER birth_date SET NOT NULL');
        $this->addSql('ALTER TABLE customers ALTER address SET NOT NULL');
    }
}
