<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160117091819 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE `group` (id INT AUTO_INCREMENT NOT NULL, leader_id INT DEFAULT NULL, status SMALLINT NOT NULL, activation_hash CHAR(32) NOT NULL, name VARCHAR(50) NOT NULL, dates_id SMALLINT NOT NULL, comments VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_6DC044C55CFA1EBA (activation_hash), UNIQUE INDEX UNIQ_6DC044C573154ED4 (leader_id), INDEX g_index_1 (status), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C573154ED4 FOREIGN KEY (leader_id) REFERENCES pilgrim (id)');
        $this->addSql('ALTER TABLE pilgrim ADD group_id INT DEFAULT NULL AFTER birth_date');
        $this->addSql('ALTER TABLE pilgrim ADD CONSTRAINT FK_74857504FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id)');
        $this->addSql('CREATE INDEX IDX_74857504FE54D947 ON pilgrim (group_id)');
        $this->addSql('ALTER TABLE troop MODIFY leader_id INT DEFAULT NULL AFTER id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pilgrim DROP FOREIGN KEY FK_74857504FE54D947');
        $this->addSql('DROP TABLE `group`');
        $this->addSql('DROP INDEX IDX_74857504FE54D947 ON pilgrim');
        $this->addSql('ALTER TABLE pilgrim DROP group_id');
        $this->addSql('ALTER TABLE troop MODIFY leader_id INT DEFAULT NULL AFTER name');
    }
}
