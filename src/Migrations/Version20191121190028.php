<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191121190028 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `release` DROP FOREIGN KEY FK_9E47031D8C24077B');
        $this->addSql('DROP INDEX IDX_9E47031D8C24077B ON `release`');
        $this->addSql('ALTER TABLE `release` DROP sprint_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `release` ADD sprint_id INT NOT NULL');
        $this->addSql('ALTER TABLE `release` ADD CONSTRAINT FK_9E47031D8C24077B FOREIGN KEY (sprint_id) REFERENCES sprint (id)');
        $this->addSql('CREATE INDEX IDX_9E47031D8C24077B ON `release` (sprint_id)');
    }
}
