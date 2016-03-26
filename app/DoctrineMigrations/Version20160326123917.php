<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160326123917 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `group` ADD updated_at DATETIME NOT NULL AFTER created_at');
        $this->addSql('UPDATE `group` SET updated_at = created_at');
        $this->addSql('ALTER TABLE `pilgrim` ADD updated_at DATETIME NOT NULL AFTER created_at');
        $this->addSql('UPDATE `pilgrim` SET updated_at = created_at');
        $this->addSql('ALTER TABLE `troop` ADD updated_at DATETIME NOT NULL AFTER created_at');
        $this->addSql('UPDATE `troop` SET updated_at = created_at');
        $this->addSql('ALTER TABLE `volunteer` ADD updated_at DATETIME NOT NULL AFTER created_at');
        $this->addSql('UPDATE `volunteer` SET updated_at = created_at');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `group` DROP updated_at');
        $this->addSql('ALTER TABLE `pilgrim` DROP updated_at');
        $this->addSql('ALTER TABLE `troop` DROP updated_at');
        $this->addSql('ALTER TABLE `volunteer` DROP updated_at');
    }
}
