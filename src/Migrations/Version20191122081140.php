<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191122081140 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE issue DROP FOREIGN KEY FK_12AD233E26EEA93A');
        $this->addSql('DROP INDEX IDX_12AD233E26EEA93A ON issue');
        $this->addSql('ALTER TABLE issue DROP linked_release_id');
        $this->addSql('ALTER TABLE sprint ADD release_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sprint ADD CONSTRAINT FK_EF8055B7B12A727D FOREIGN KEY (release_id) REFERENCES `release` (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EF8055B7B12A727D ON sprint (release_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE issue ADD linked_release_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233E26EEA93A FOREIGN KEY (linked_release_id) REFERENCES `release` (id)');
        $this->addSql('CREATE INDEX IDX_12AD233E26EEA93A ON issue (linked_release_id)');
        $this->addSql('ALTER TABLE sprint DROP FOREIGN KEY FK_EF8055B7B12A727D');
        $this->addSql('DROP INDEX UNIQ_EF8055B7B12A727D ON sprint');
        $this->addSql('ALTER TABLE sprint DROP release_id');
    }
}
