<?php

namespace DmarcDash\Db\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150817041836 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX reportId_sourceIp_identifierHeaderFrom_unique ON ReportRecord');
        $this->addSql('CREATE INDEX reportId_idx ON ReportRecord (reportId)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX reportId_idx ON ReportRecord');
        $this->addSql('CREATE UNIQUE INDEX reportId_sourceIp_identifierHeaderFrom_unique ON ReportRecord (reportId, sourceIp, identifierHeaderFrom)');
    }
}
