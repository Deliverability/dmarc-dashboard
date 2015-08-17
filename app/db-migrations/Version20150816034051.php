<?php

namespace DmarcDash\Db\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150816034051 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE Domain (id INT AUTO_INCREMENT NOT NULL, userId INT NOT NULL, domainName VARCHAR(255) NOT NULL, datetimeCreated DATETIME NOT NULL COMMENT \'In UTC\', datetimeUpdated DATETIME NOT NULL COMMENT \'In UTC\', INDEX userId_idx (userId), UNIQUE INDEX domainName_unique (domainName), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Report (id INT AUTO_INCREMENT NOT NULL, domainId INT NOT NULL, reportDomain VARCHAR(255) NOT NULL, submittedReportId VARCHAR(255) NOT NULL, timestampStart INT NOT NULL, timestampEnd INT NOT NULL, policyPublishedDomain VARCHAR(255) NOT NULL, policyPublishedAdkim VARCHAR(255) NOT NULL, policyPublishedAspf VARCHAR(255) NOT NULL, policyPublishedP VARCHAR(255) NOT NULL, policyPublishedSp VARCHAR(255) NOT NULL, policyPublishedPct INT NOT NULL, datetimeCreated DATETIME NOT NULL COMMENT \'In UTC\', datetimeUpdated DATETIME NOT NULL COMMENT \'In UTC\', INDEX timestampStart_idx (timestampStart), INDEX timestampEnd_idx (timestampEnd), UNIQUE INDEX domainId_reportDomain_submittedReportId_unique (domainId, reportDomain, submittedReportId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ReportRecord (id INT AUTO_INCREMENT NOT NULL, reportId INT NOT NULL, sourceIp VARCHAR(255) NOT NULL, messageCount INT NOT NULL, policyEvaluatedDisposition VARCHAR(255) NOT NULL, policyEvaluatedDkim VARCHAR(255) NOT NULL, policyEvaluatedSpf VARCHAR(255) NOT NULL, identifierHeaderFrom VARCHAR(255) NOT NULL, authResultDkimDomain VARCHAR(255) NOT NULL, authResultDkimResult VARCHAR(255) NOT NULL, authResultSpfDomain VARCHAR(255) NOT NULL, authResultSpfResult VARCHAR(255) NOT NULL, datetimeCreated DATETIME NOT NULL COMMENT \'In UTC\', datetimeUpdated DATETIME NOT NULL COMMENT \'In UTC\', INDEX authResultDkimResult_idx (authResultDkimResult), INDEX authResultSpfResult_idx (authResultSpfResult), UNIQUE INDEX reportId_sourceIp_identifierHeaderFrom_unique (reportId, sourceIp, identifierHeaderFrom), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE Domain');
        $this->addSql('DROP TABLE Report');
        $this->addSql('DROP TABLE ReportRecord');
    }
}
