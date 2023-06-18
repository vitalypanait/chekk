<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230612063252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add owner to board';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boards ADD owner_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN boards.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE boards ADD CONSTRAINT FK_F3EE4D137E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_F3EE4D137E3C61F9 ON boards (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boards DROP CONSTRAINT FK_F3EE4D137E3C61F9');
        $this->addSql('DROP INDEX IDX_F3EE4D137E3C61F9');
        $this->addSql('ALTER TABLE boards DROP owner_id');
    }
}
