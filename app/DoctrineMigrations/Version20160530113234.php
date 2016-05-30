<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160530113234 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE d_bitacora_serial ADD qty SMALLINT DEFAULT NULL, CHANGE movement_type movement_type VARCHAR(2) NOT NULL, CHANGE serial serial VARCHAR(32) DEFAULT NULL');
        $this->addSql('CREATE INDEX serial_idx ON d_bitacora_serial (serial)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX serial_idx ON d_bitacora_serial');
        $this->addSql('ALTER TABLE d_bitacora_serial DROP qty, CHANGE movement_type movement_type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE serial serial VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
