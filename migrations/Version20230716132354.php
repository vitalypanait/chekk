<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230716132354 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Board pin code';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE board_ids ADD pin_code VARCHAR(64) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE board_ids DROP pin_code');
    }
}
