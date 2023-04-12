<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230410124120 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create table board';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE boards (
                id UUID NOT NULL UNIQUE,
                title VARCHAR(1000) DEFAULT NULL,
                created_at TIMESTAMP NOT NULL,
                updated_at TIMESTAMP NOT NULL,
                PRIMARY KEY(id)
            )
        ');
        $this->addSql('
            CREATE TABLE tasks (
                id UUID NOT NULL UNIQUE,
                board_id UUID NULL NULL,
                title VARCHAR(1000) NOT NULL,
                state VARCHAR(10) DEFAULT NULL,
                created_at TIMESTAMP NOT NULL,
                updated_at TIMESTAMP NOT NULL,
                PRIMARY KEY(id),
                FOREIGN KEY (board_id) REFERENCES boards (id)
           )
       ');
        $this->addSql('
            CREATE TABLE comments (
                id UUID NOT NULL UNIQUE,
                task_id UUID NULL NULL,
                content TEXT NOT NULL,
                created_at TIMESTAMP NOT NULL,
                updated_at TIMESTAMP NOT NULL,
                PRIMARY KEY(id),
                FOREIGN KEY (task_id) REFERENCES tasks (id)
           )
       ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE boards');
        $this->addSql('DROP TABLE tasks');
        $this->addSql('DROP TABLE comments');
    }
}
