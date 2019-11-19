<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191119143909 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sprint (id INT AUTO_INCREMENT NOT NULL, project_id INT NOT NULL, name VARCHAR(64) NOT NULL, description VARCHAR(256) NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, INDEX IDX_EF8055B7166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sprint ADD CONSTRAINT FK_EF8055B7166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE `release` DROP FOREIGN KEY FK_9E47031D166D1F9C');
        $this->addSql('DROP INDEX IDX_9E47031D166D1F9C ON `release`');
        $this->addSql('ALTER TABLE `release` CHANGE project_id sprint_id INT NOT NULL');
        $this->addSql('ALTER TABLE `release` ADD CONSTRAINT FK_9E47031D8C24077B FOREIGN KEY (sprint_id) REFERENCES sprint (id)');
        $this->addSql('CREATE INDEX IDX_9E47031D8C24077B ON `release` (sprint_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `release` DROP FOREIGN KEY FK_9E47031D8C24077B');
        $this->addSql('DROP TABLE sprint');
        $this->addSql('DROP INDEX IDX_9E47031D8C24077B ON `release`');
        $this->addSql('ALTER TABLE `release` CHANGE sprint_id project_id INT NOT NULL');
        $this->addSql('ALTER TABLE `release` ADD CONSTRAINT FK_9E47031D166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('CREATE INDEX IDX_9E47031D166D1F9C ON `release` (project_id)');
    }
}
