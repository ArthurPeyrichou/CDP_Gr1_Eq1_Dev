<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191129171206 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE documentation ADD project_id INT NOT NULL');
        $this->addSql('ALTER TABLE documentation ADD CONSTRAINT FK_73D5A93B166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_73D5A93B166D1F9C ON documentation (project_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE documentation DROP FOREIGN KEY FK_73D5A93B166D1F9C');
        $this->addSql('DROP INDEX UNIQ_73D5A93B166D1F9C ON documentation');
        $this->addSql('ALTER TABLE documentation DROP project_id');
    }
}
