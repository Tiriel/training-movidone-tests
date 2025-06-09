<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250219123418 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE volunteer_profile_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE event_tag (event_id INT NOT NULL, tag_id INT NOT NULL, PRIMARY KEY(event_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_1246725071F7E88B ON event_tag (event_id)');
        $this->addSql('CREATE INDEX IDX_12467250BAD26311 ON event_tag (tag_id)');
        $this->addSql('CREATE TABLE project_tag (project_id INT NOT NULL, tag_id INT NOT NULL, PRIMARY KEY(project_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_91F26D60166D1F9C ON project_tag (project_id)');
        $this->addSql('CREATE INDEX IDX_91F26D60BAD26311 ON project_tag (tag_id)');
        $this->addSql('CREATE TABLE tag (id INT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE volunteer_profile (id INT NOT NULL, for_user_id INT NOT NULL, skills JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5FBFB5379B5BB4B8 ON volunteer_profile (for_user_id)');
        $this->addSql('CREATE TABLE volunteer_profile_tag (volunteer_profile_id INT NOT NULL, tag_id INT NOT NULL, PRIMARY KEY(volunteer_profile_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_7802AC888509C29A ON volunteer_profile_tag (volunteer_profile_id)');
        $this->addSql('CREATE INDEX IDX_7802AC88BAD26311 ON volunteer_profile_tag (tag_id)');
        $this->addSql('ALTER TABLE event_tag ADD CONSTRAINT FK_1246725071F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE event_tag ADD CONSTRAINT FK_12467250BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project_tag ADD CONSTRAINT FK_91F26D60166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project_tag ADD CONSTRAINT FK_91F26D60BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE volunteer_profile ADD CONSTRAINT FK_5FBFB5379B5BB4B8 FOREIGN KEY (for_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE volunteer_profile_tag ADD CONSTRAINT FK_7802AC888509C29A FOREIGN KEY (volunteer_profile_id) REFERENCES volunteer_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE volunteer_profile_tag ADD CONSTRAINT FK_7802AC88BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE tag_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE volunteer_profile_id_seq CASCADE');
        $this->addSql('ALTER TABLE event_tag DROP CONSTRAINT FK_1246725071F7E88B');
        $this->addSql('ALTER TABLE event_tag DROP CONSTRAINT FK_12467250BAD26311');
        $this->addSql('ALTER TABLE project_tag DROP CONSTRAINT FK_91F26D60166D1F9C');
        $this->addSql('ALTER TABLE project_tag DROP CONSTRAINT FK_91F26D60BAD26311');
        $this->addSql('ALTER TABLE volunteer_profile DROP CONSTRAINT FK_5FBFB5379B5BB4B8');
        $this->addSql('ALTER TABLE volunteer_profile_tag DROP CONSTRAINT FK_7802AC888509C29A');
        $this->addSql('ALTER TABLE volunteer_profile_tag DROP CONSTRAINT FK_7802AC88BAD26311');
        $this->addSql('DROP TABLE event_tag');
        $this->addSql('DROP TABLE project_tag');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE volunteer_profile');
        $this->addSql('DROP TABLE volunteer_profile_tag');
    }
}
