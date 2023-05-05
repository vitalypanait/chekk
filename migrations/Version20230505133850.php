<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230505133850 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add labels and task_labels';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE labels (id UUID NOT NULL, board_id UUID NOT NULL, title VARCHAR(255) NOT NULL, created_at TIMESTAMP NOT NULL, updated_at TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B5D10211E7EC5785 ON labels (board_id)');
        $this->addSql('CREATE TABLE task_labels (id UUID NOT NULL, task_id UUID NOT NULL, label_id UUID NOT NULL, created_at TIMESTAMP NOT NULL, updated_at TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8E7886C28DB60186 ON task_labels (task_id)');
        $this->addSql('CREATE INDEX IDX_8E7886C233B92F39 ON task_labels (label_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8E7886C28DB6018633B92F39 ON task_labels (task_id, label_id)');
        $this->addSql('ALTER TABLE labels ADD CONSTRAINT FK_B5D10211E7EC5785 FOREIGN KEY (board_id) REFERENCES boards (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_labels ADD CONSTRAINT FK_8E7886C28DB60186 FOREIGN KEY (task_id) REFERENCES tasks (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_labels ADD CONSTRAINT FK_8E7886C233B92F39 FOREIGN KEY (label_id) REFERENCES labels (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE labels DROP CONSTRAINT FK_B5D10211E7EC5785');
        $this->addSql('ALTER TABLE task_labels DROP CONSTRAINT FK_8E7886C28DB60186');
        $this->addSql('ALTER TABLE task_labels DROP CONSTRAINT FK_8E7886C233B92F39');
        $this->addSql('DROP TABLE labels');
        $this->addSql('DROP TABLE task_labels');
    }
}
