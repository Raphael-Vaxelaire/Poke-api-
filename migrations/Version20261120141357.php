<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261120141357 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pokemon ADD COLUMN pokemon_id INTEGER NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__pokemon AS SELECT id, name, height, weight, original_form, generation_id FROM pokemon');
        $this->addSql('DROP TABLE pokemon');
        $this->addSql('CREATE TABLE pokemon (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, height INTEGER DEFAULT NULL, weight INTEGER DEFAULT NULL, original_form BOOLEAN NOT NULL, generation_id INTEGER NOT NULL, CONSTRAINT FK_62DC90F3553A6EC4 FOREIGN KEY (generation_id) REFERENCES generation (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO pokemon (id, name, height, weight, original_form, generation_id) SELECT id, name, height, weight, original_form, generation_id FROM __temp__pokemon');
        $this->addSql('DROP TABLE __temp__pokemon');
        $this->addSql('CREATE INDEX IDX_62DC90F3553A6EC4 ON pokemon (generation_id)');
    }
}
