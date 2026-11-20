<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261120133757 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pokemon (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, height INTEGER DEFAULT NULL, weight INTEGER DEFAULT NULL, original_form BOOLEAN NOT NULL, generation_id INTEGER NOT NULL, CONSTRAINT FK_62DC90F3553A6EC4 FOREIGN KEY (generation_id) REFERENCES generation (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_62DC90F3553A6EC4 ON pokemon (generation_id)');
        $this->addSql('CREATE TABLE pokemon_type (pokemon_id INTEGER NOT NULL, type_id INTEGER NOT NULL, PRIMARY KEY (pokemon_id, type_id), CONSTRAINT FK_B077296A2FE71C3E FOREIGN KEY (pokemon_id) REFERENCES pokemon (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_B077296AC54C8C93 FOREIGN KEY (type_id) REFERENCES type (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_B077296A2FE71C3E ON pokemon_type (pokemon_id)');
        $this->addSql('CREATE INDEX IDX_B077296AC54C8C93 ON pokemon_type (type_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__generation AS SELECT id, number FROM generation');
        $this->addSql('DROP TABLE generation');
        $this->addSql('CREATE TABLE generation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, number INTEGER NOT NULL)');
        $this->addSql('INSERT INTO generation (id, number) SELECT id, number FROM __temp__generation');
        $this->addSql('DROP TABLE __temp__generation');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D3266C3B96901F54 ON generation (number)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__type AS SELECT id, name FROM type');
        $this->addSql('DROP TABLE type');
        $this->addSql('CREATE TABLE type (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO type (id, name) SELECT id, name FROM __temp__type');
        $this->addSql('DROP TABLE __temp__type');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8CDE57295E237E06 ON type (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE pokemon');
        $this->addSql('DROP TABLE pokemon_type');
        $this->addSql('CREATE TEMPORARY TABLE __temp__generation AS SELECT id, number FROM generation');
        $this->addSql('DROP TABLE generation');
        $this->addSql('CREATE TABLE generation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, number INTEGER NOT NULL)');
        $this->addSql('INSERT INTO generation (id, number) SELECT id, number FROM __temp__generation');
        $this->addSql('DROP TABLE __temp__generation');
        $this->addSql('CREATE TEMPORARY TABLE __temp__type AS SELECT id, name FROM type');
        $this->addSql('DROP TABLE type');
        $this->addSql('CREATE TABLE type (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO type (id, name) SELECT id, name FROM __temp__type');
        $this->addSql('DROP TABLE __temp__type');
    }
}
