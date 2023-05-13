<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230513060734 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add archived at to the task';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tasks ADD archived_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tasks DROP archived_at');
    }
}
