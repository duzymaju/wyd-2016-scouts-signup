<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160105180638 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE troop ADD dates_id SMALLINT NOT NULL AFTER leader_id, DROP date_from, DROP date_to');
        $this->addSql('ALTER TABLE volunteer ADD dates_id SMALLINT NOT NULL AFTER profession, DROP date_from, DROP date_to');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE troop ADD date_from DATE NOT NULL AFTER leader_id, ADD date_to DATE NOT NULL AFTER date_from, DROP dates_id');
        $this->addSql('ALTER TABLE volunteer ADD date_from DATE NOT NULL AFTER profession, ADD date_to DATE NOT NULL AFTER date_from, DROP dates_id');
    }
}
