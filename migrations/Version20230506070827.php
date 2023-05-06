<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230506070827 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add color to label';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE labels ADD color VARCHAR(15) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE labels DROP color');
    }
}
