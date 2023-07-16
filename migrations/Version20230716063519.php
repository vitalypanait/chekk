<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230716063519 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Board id entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE board_ids (id UUID NOT NULL, board_id UUID NOT NULL, read_only BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_863B54F3E7EC5785 ON board_ids (board_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_863B54F3BF396750E7EC5785 ON board_ids (id, board_id)');
        $this->addSql('COMMENT ON COLUMN board_ids.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN board_ids.board_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE board_ids ADD CONSTRAINT FK_863B54F3E7EC5785 FOREIGN KEY (board_id) REFERENCES boards (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('INSERT INTO board_ids (id, board_id, read_only, created_at, updated_at) SELECT b.id, b.id, false, b.created_at, NOW() FROM boards as b');
        $this->addSql('INSERT INTO board_ids (id, board_id, read_only, created_at, updated_at) SELECT b.read_only_id, b.id, true, b.created_at, NOW() FROM boards as b where b.read_only_id is not null');
        $this->addSql('ALTER TABLE boards DROP read_only_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE board_ids DROP CONSTRAINT FK_863B54F3E7EC5785');
        $this->addSql('DROP TABLE board_ids');
        $this->addSql('ALTER TABLE boards ALTER display SET DEFAULT \'task\'');
        $this->addSql('COMMENT ON COLUMN boards.read_only_id IS \'(DC2Type:uuid)\'');
    }
}
