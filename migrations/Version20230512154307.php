<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20230512154307 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update color length';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE labels ALTER COLUMN color TYPE varchar(50)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE labels ALTER COLUMN color TYPE varchar(15)');
    }
}