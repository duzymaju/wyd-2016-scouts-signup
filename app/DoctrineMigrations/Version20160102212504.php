<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160102212504 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pilgrim ADD comments VARCHAR(255) DEFAULT NULL AFTER date_to');
        $this->addSql('ALTER TABLE troop ADD comments VARCHAR(255) DEFAULT NULL AFTER date_to');
        $this->addSql('ALTER TABLE volunteer ADD birth_date DATE NOT NULL AFTER last_name, ADD country CHAR(2) NOT NULL AFTER birth_date, ADD comments VARCHAR(255) DEFAULT NULL AFTER date_to');
        $this->addSql('ALTER TABLE volunteer MODIFY address VARCHAR(255) NOT NULL AFTER country');
        $this->addSql('ALTER TABLE volunteer MODIFY phone VARCHAR(15) NOT NULL AFTER address');
        $this->addSql('ALTER TABLE volunteer MODIFY email VARCHAR(40) NOT NULL AFTER phone');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pilgrim DROP comments');
        $this->addSql('ALTER TABLE troop DROP comments');
        $this->addSql('ALTER TABLE volunteer DROP birth_date, DROP country, DROP comments');
        $this->addSql('ALTER TABLE volunteer MODIFY address VARCHAR(255) NOT NULL AFTER pesel');
        $this->addSql('ALTER TABLE volunteer MODIFY phone VARCHAR(15) NOT NULL AFTER profession');
        $this->addSql('ALTER TABLE volunteer MODIFY email VARCHAR(40) NOT NULL AFTER phone');
    }
}
