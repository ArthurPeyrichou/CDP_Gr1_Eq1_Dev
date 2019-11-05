<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191105195557 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_70E4FA785126AC48 ON member');
        $this->addSql('DROP INDEX UNIQ_70E4FA7876348E1B67B341FC ON member');
        $this->addSql('DROP INDEX UNIQ_70E4FA7886CC499D ON member');
        $this->addSql('ALTER TABLE member ADD name VARCHAR(50) NOT NULL, ADD email_address VARCHAR(128) NOT NULL, DROP pseudo, DROP mail, CHANGE password password VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_70E4FA785E237E06 ON member (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_70E4FA78B08E074E ON member (email_address)');
        $this->addSql('ALTER TABLE project CHANGE name name VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_70E4FA785E237E06 ON member');
        $this->addSql('DROP INDEX UNIQ_70E4FA78B08E074E ON member');
        $this->addSql('ALTER TABLE member ADD mail VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci, DROP email_address, CHANGE password password VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE name pseudo VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_70E4FA785126AC48 ON member (mail)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_70E4FA7876348E1B67B341FC ON member (pseudo, mail)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_70E4FA7886CC499D ON member (pseudo)');
        $this->addSql('ALTER TABLE project CHANGE name name INT NOT NULL');
    }
}
