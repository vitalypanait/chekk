<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230523144452 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add display to a board';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE boards ADD display VARCHAR(50) NOT NULL DEFAULT \'task\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE boards DROP display');
    }
}
