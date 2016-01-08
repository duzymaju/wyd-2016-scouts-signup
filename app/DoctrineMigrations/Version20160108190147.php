<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160108190147 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pilgrim CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE troop CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE volunteer CHANGE created_at created_at DATETIME NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pilgrim CHANGE created_at created_at DATE NOT NULL');
        $this->addSql('ALTER TABLE troop CHANGE created_at created_at DATE NOT NULL');
        $this->addSql('ALTER TABLE volunteer CHANGE created_at created_at DATE NOT NULL');
    }
}
