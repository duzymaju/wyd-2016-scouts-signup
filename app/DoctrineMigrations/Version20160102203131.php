<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160102203131 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX sa_index_1 ON pilgrim');
        $this->addSql('CREATE INDEX p_index_1 ON pilgrim (status)');
        $this->addSql('DROP INDEX sa_index_1 ON volunteer');
        $this->addSql('CREATE INDEX v_index_1 ON volunteer (status)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX p_index_1 ON pilgrim');
        $this->addSql('CREATE INDEX sa_index_1 ON pilgrim (status)');
        $this->addSql('DROP INDEX v_index_1 ON volunteer');
        $this->addSql('CREATE INDEX sa_index_1 ON volunteer (status)');
    }
}
