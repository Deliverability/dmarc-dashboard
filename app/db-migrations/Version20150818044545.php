<?php

namespace DmarcDash\Db\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150818044545 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE User ADD imapEnabled INT NOT NULL, ADD imapHost VARCHAR(255) NOT NULL, ADD imapPort VARCHAR(255) NOT NULL, ADD imapProtocol VARCHAR(255) NOT NULL, ADD imapUsername VARCHAR(255) NOT NULL, ADD imapPassword VARCHAR(255) NOT NULL');
        $this->addSql('CREATE INDEX imapEnabled_idx ON User (imapEnabled)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX imapEnabled_idx ON User');
        $this->addSql('ALTER TABLE User DROP imapEnabled, DROP imapHost, DROP imapPort, DROP imapProtocol, DROP imapUsername, DROP imapPassword');
    }
}
