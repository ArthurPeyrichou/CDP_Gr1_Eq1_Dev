<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191117153053 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, developper_id INT DEFAULT NULL, project_id INT NOT NULL, number INT NOT NULL, description VARCHAR(255) NOT NULL, required_man_days DOUBLE PRECISION NOT NULL, status VARCHAR(8) NOT NULL, INDEX IDX_527EDB25DA42B93 (developper_id), INDEX IDX_527EDB25166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task_issue (task_id INT NOT NULL, issue_id INT NOT NULL, INDEX IDX_D509381E8DB60186 (task_id), INDEX IDX_D509381E5E7AA58C (issue_id), PRIMARY KEY(task_id, issue_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25DA42B93 FOREIGN KEY (developper_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE task_issue ADD CONSTRAINT FK_D509381E8DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task_issue ADD CONSTRAINT FK_D509381E5E7AA58C FOREIGN KEY (issue_id) REFERENCES issue (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE issue CHANGE linked_to_release_id linked_to_release_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `release` ADD project_id INT NOT NULL');
        $this->addSql('ALTER TABLE `release` ADD CONSTRAINT FK_9E47031D166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('CREATE INDEX IDX_9E47031D166D1F9C ON `release` (project_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE task_issue DROP FOREIGN KEY FK_D509381E8DB60186');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE task_issue');
        $this->addSql('ALTER TABLE issue CHANGE linked_to_release_id linked_to_release_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `release` DROP FOREIGN KEY FK_9E47031D166D1F9C');
        $this->addSql('DROP INDEX IDX_9E47031D166D1F9C ON `release`');
        $this->addSql('ALTER TABLE `release` DROP project_id');
    }
}
