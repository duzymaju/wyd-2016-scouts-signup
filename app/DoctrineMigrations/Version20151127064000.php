<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151127064000 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE pilgrim_application (id INT AUTO_INCREMENT NOT NULL, status SMALLINT NOT NULL, activation_hash CHAR(32) NOT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, country CHAR(2) NOT NULL, address VARCHAR(255) NOT NULL, phone VARCHAR(15) NOT NULL, email VARCHAR(40) NOT NULL, accomodation_id SMALLINT NOT NULL, date_from DATE NOT NULL, date_to DATE NOT NULL, created_at DATE NOT NULL, UNIQUE INDEX UNIQ_DBF6FA6D5CFA1EBA (activation_hash), UNIQUE INDEX UNIQ_DBF6FA6DE7927C74 (email), INDEX sa_index_1 (status), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scout_application (id INT AUTO_INCREMENT NOT NULL, status SMALLINT NOT NULL, activation_hash CHAR(32) NOT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, grade_id SMALLINT NOT NULL, region_id SMALLINT NOT NULL, pesel BIGINT NOT NULL, address VARCHAR(255) NOT NULL, service_id SMALLINT NOT NULL, permissions VARCHAR(255) NOT NULL, languages VARCHAR(255) NOT NULL, profession VARCHAR(255) NOT NULL, phone VARCHAR(15) NOT NULL, email VARCHAR(40) NOT NULL, service_time VARCHAR(255) NOT NULL, created_at DATE NOT NULL, UNIQUE INDEX UNIQ_6D4CB92B5CFA1EBA (activation_hash), UNIQUE INDEX UNIQ_6D4CB92B3931747B (pesel), UNIQUE INDEX UNIQ_6D4CB92BE7927C74 (email), INDEX sa_index_1 (status), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE pilgrim_application');
        $this->addSql('DROP TABLE scout_application');
    }
}
