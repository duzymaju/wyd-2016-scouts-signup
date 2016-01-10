<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160110150912 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pilgrim DROP own_tent');
        $this->addSql('ALTER TABLE troop DROP own_tent');
        $this->addSql('ALTER TABLE volunteer DROP own_tent');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pilgrim ADD own_tent TINYINT(1) NOT NULL AFTER birth_date');
        $this->addSql('ALTER TABLE troop ADD own_tent TINYINT(1) NOT NULL AFTER leader_id');
        $this->addSql('ALTER TABLE volunteer ADD own_tent TINYINT(1) NOT NULL AFTER profession');
    }
}
