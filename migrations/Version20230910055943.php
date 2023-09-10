<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230910055943 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add board visited history';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE board_visited_history (id UUID NOT NULL, board_id UUID NOT NULL, user_id UUID DEFAULT NULL, visited_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_597C5E85E7EC5785 ON board_visited_history (board_id)');
        $this->addSql('CREATE INDEX IDX_597C5E85A76ED395 ON board_visited_history (user_id)');
        $this->addSql('COMMENT ON COLUMN board_visited_history.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN board_visited_history.board_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN board_visited_history.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE board_visited_history ADD CONSTRAINT FK_597C5E85E7EC5785 FOREIGN KEY (board_id) REFERENCES board_ids (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE board_visited_history ADD CONSTRAINT FK_597C5E85A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE board_visited_history DROP CONSTRAINT FK_597C5E85E7EC5785');
        $this->addSql('ALTER TABLE board_visited_history DROP CONSTRAINT FK_597C5E85A76ED395');
        $this->addSql('DROP TABLE board_visited_history');
    }
}
