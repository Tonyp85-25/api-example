<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210824092518 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER NOT NULL, content CLOB NOT NULL, published DATETIME NOT NULL)');
        $this->addSql('CREATE INDEX IDX_9474526CF675F31B ON comment (author_id)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__blog_post AS SELECT id, title, published, content, slug FROM blog_post');
        $this->addSql('DROP TABLE blog_post');
        $this->addSql('CREATE TABLE blog_post (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL COLLATE BINARY, published DATETIME NOT NULL, content CLOB NOT NULL COLLATE BINARY, slug VARCHAR(255) DEFAULT NULL COLLATE BINARY)');
        $this->addSql('INSERT INTO blog_post (id, title, published, content, slug) SELECT id, title, published, content, slug FROM __temp__blog_post');
        $this->addSql('DROP TABLE __temp__blog_post');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE blog_post ADD COLUMN author VARCHAR(255) NOT NULL COLLATE BINARY');
    }
}
