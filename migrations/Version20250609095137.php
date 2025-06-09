<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250609095137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE volunteer_profile ADD address VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE volunteer_profile ADD latitude NUMERIC(10, 8) DEFAULT NULL');
        $this->addSql('ALTER TABLE volunteer_profile ADD longitude NUMERIC(11, 8) DEFAULT NULL');
        $this->addSql('ALTER TABLE volunteer_profile ADD postal_code VARCHAR(10) DEFAULT NULL');
        $this->addSql('ALTER TABLE volunteer_profile ADD city VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE volunteer_profile ADD country VARCHAR(100) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE volunteer_profile DROP address');
        $this->addSql('ALTER TABLE volunteer_profile DROP latitude');
        $this->addSql('ALTER TABLE volunteer_profile DROP longitude');
        $this->addSql('ALTER TABLE volunteer_profile DROP postal_code');
        $this->addSql('ALTER TABLE volunteer_profile DROP city');
        $this->addSql('ALTER TABLE volunteer_profile DROP country');
    }
}
