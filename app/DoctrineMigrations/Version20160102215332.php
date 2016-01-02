<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160102215332 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_5140DEDB3931747B ON volunteer');
        $this->addSql('ALTER TABLE pilgrim MODIFY country CHAR(2) NOT NULL AFTER email, MODIFY birth_date DATE NOT NULL AFTER country');
        $this->addSql('ALTER TABLE volunteer ADD district_id SMALLINT DEFAULT NULL AFTER region_id, CHANGE grade_id grade_id SMALLINT DEFAULT NULL, CHANGE region_id region_id SMALLINT DEFAULT NULL, CHANGE pesel pesel BIGINT DEFAULT NULL, MODIFY country CHAR(2) NOT NULL AFTER email, MODIFY birth_date DATE NOT NULL AFTER country');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pilgrim MODIFY birth_date DATE NOT NULL AFTER last_name, MODIFY country CHAR(2) NOT NULL AFTER birth_date');
        $this->addSql('ALTER TABLE volunteer DROP district_id, CHANGE grade_id grade_id SMALLINT NOT NULL, CHANGE region_id region_id SMALLINT NOT NULL, CHANGE pesel pesel BIGINT NOT NULL, MODIFY birth_date DATE NOT NULL AFTER last_name, MODIFY country CHAR(2) NOT NULL AFTER birth_date');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5140DEDB3931747B ON volunteer (pesel)');
    }
}
