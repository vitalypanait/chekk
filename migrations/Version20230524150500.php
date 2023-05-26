<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230524150500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add read only id for board';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE boards ADD read_only_id UUID DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE boards DROP readonly_id');
    }
}
