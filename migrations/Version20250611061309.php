<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250611061309 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD address VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD latitude NUMERIC(10, 8) DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD longitude NUMERIC(11, 8) DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD postal_code VARCHAR(10) DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD city VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD country VARCHAR(100) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE event DROP address');
        $this->addSql('ALTER TABLE event DROP latitude');
        $this->addSql('ALTER TABLE event DROP longitude');
        $this->addSql('ALTER TABLE event DROP postal_code');
        $this->addSql('ALTER TABLE event DROP city');
        $this->addSql('ALTER TABLE event DROP country');
    }
}
