<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250225145405 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE skill_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE event_skill (event_id INT NOT NULL, skill_id INT NOT NULL, PRIMARY KEY(event_id, skill_id))');
        $this->addSql('CREATE INDEX IDX_1F26555B71F7E88B ON event_skill (event_id)');
        $this->addSql('CREATE INDEX IDX_1F26555B5585C142 ON event_skill (skill_id)');
        $this->addSql('CREATE TABLE skill (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE volunteer_profile_skill (volunteer_profile_id INT NOT NULL, skill_id INT NOT NULL, PRIMARY KEY(volunteer_profile_id, skill_id))');
        $this->addSql('CREATE INDEX IDX_37AC8FDB8509C29A ON volunteer_profile_skill (volunteer_profile_id)');
        $this->addSql('CREATE INDEX IDX_37AC8FDB5585C142 ON volunteer_profile_skill (skill_id)');
        $this->addSql('ALTER TABLE event_skill ADD CONSTRAINT FK_1F26555B71F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE event_skill ADD CONSTRAINT FK_1F26555B5585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE volunteer_profile_skill ADD CONSTRAINT FK_37AC8FDB8509C29A FOREIGN KEY (volunteer_profile_id) REFERENCES volunteer_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE volunteer_profile_skill ADD CONSTRAINT FK_37AC8FDB5585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE volunteer_profile DROP skills');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE skill_id_seq CASCADE');
        $this->addSql('ALTER TABLE event_skill DROP CONSTRAINT FK_1F26555B71F7E88B');
        $this->addSql('ALTER TABLE event_skill DROP CONSTRAINT FK_1F26555B5585C142');
        $this->addSql('ALTER TABLE volunteer_profile_skill DROP CONSTRAINT FK_37AC8FDB8509C29A');
        $this->addSql('ALTER TABLE volunteer_profile_skill DROP CONSTRAINT FK_37AC8FDB5585C142');
        $this->addSql('DROP TABLE event_skill');
        $this->addSql('DROP TABLE skill');
        $this->addSql('DROP TABLE volunteer_profile_skill');
        $this->addSql('ALTER TABLE volunteer_profile ADD skills JSON NOT NULL');
    }
}
