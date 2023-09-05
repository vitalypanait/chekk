<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230902022557 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add theme to boards';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boards ADD theme VARCHAR(50) NOT NULL DEFAULT \'light\'');
        $this->addSql('UPDATE boards set theme = \'light\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boards DROP theme');
    }
}
