<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160102202829 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE troop (id INT AUTO_INCREMENT NOT NULL, status SMALLINT NOT NULL, activation_hash CHAR(32) NOT NULL, name VARCHAR(50) NOT NULL, leader_id INT DEFAULT NULL, date_from DATE NOT NULL, date_to DATE NOT NULL, created_at DATE NOT NULL, UNIQUE INDEX UNIQ_FAAD534C5CFA1EBA (activation_hash), UNIQUE INDEX UNIQ_FAAD534C73154ED4 (leader_id), INDEX t_index_1 (status), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE troop ADD CONSTRAINT FK_FAAD534C73154ED4 FOREIGN KEY (leader_id) REFERENCES volunteer (id)');
        $this->addSql('ALTER TABLE volunteer ADD troop_id INT DEFAULT NULL AFTER region_id');
        $this->addSql('ALTER TABLE volunteer ADD CONSTRAINT FK_5140DEDB263060AC FOREIGN KEY (troop_id) REFERENCES troop (id)');
        $this->addSql('CREATE INDEX IDX_5140DEDB263060AC ON volunteer (troop_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE volunteer DROP FOREIGN KEY FK_5140DEDB263060AC');
        $this->addSql('DROP TABLE troop');
        $this->addSql('DROP INDEX IDX_5140DEDB263060AC ON volunteer');
        $this->addSql('ALTER TABLE volunteer DROP troop_id');
    }
}
