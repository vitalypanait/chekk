<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230910045213 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Theme to theme color';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boards ADD theme_color CHAR(6) NOT NULL DEFAULT \'000000\'');
        $this->addSql('ALTER TABLE boards DROP theme');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boards ADD theme VARCHAR(50) DEFAULT \'light\' NOT NULL');
        $this->addSql('ALTER TABLE boards DROP theme_color');
    }
}
