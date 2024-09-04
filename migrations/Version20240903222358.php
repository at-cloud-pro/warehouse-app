<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240903222358 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE local_user (
                id BINARY(16) NOT NULL,
                identifier_type VARCHAR(10) NOT NULL,
                identifier_value VARCHAR(255) NOT NULL,
                jwt LONGTEXT NOT NULL,
                name VARCHAR(255) NULL,
                roles JSON NOT NULL,
                PRIMARY KEY(id)
            )
            DEFAULT CHARACTER SET utf8mb4
            COLLATE `utf8mb4_unicode_ci`
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE local_user');
    }
}
