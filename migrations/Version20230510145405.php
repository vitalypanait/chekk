<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230510145405 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add position to the task';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tasks ADD position INT NOT NULL DEFAULT 0');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tasks DROP position');
    }
}
