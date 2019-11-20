<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191119230059 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE issue CHANGE linked_to_release_id linked_to_release_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `release` ADD project_id INT NOT NULL');
        $this->addSql('ALTER TABLE `release` ADD CONSTRAINT FK_9E47031D166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('CREATE INDEX IDX_9E47031D166D1F9C ON `release` (project_id)');
        $this->addSql('ALTER TABLE task CHANGE developper_id developper_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE issue CHANGE linked_to_release_id linked_to_release_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `release` DROP FOREIGN KEY FK_9E47031D166D1F9C');
        $this->addSql('DROP INDEX IDX_9E47031D166D1F9C ON `release`');
        $this->addSql('ALTER TABLE `release` DROP project_id');
        $this->addSql('ALTER TABLE task CHANGE developper_id developper_id INT DEFAULT NULL');
    }
}
