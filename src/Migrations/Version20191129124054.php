<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191129124054 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE planning_poker DROP FOREIGN KEY FK_2F1AE12B8DB60186');
        $this->addSql('DROP INDEX IDX_2F1AE12B8DB60186 ON planning_poker');
        $this->addSql('ALTER TABLE planning_poker CHANGE task_id issue_id INT NOT NULL');
        $this->addSql('ALTER TABLE planning_poker ADD CONSTRAINT FK_2F1AE12B5E7AA58C FOREIGN KEY (issue_id) REFERENCES issue (id)');
        $this->addSql('CREATE INDEX IDX_2F1AE12B5E7AA58C ON planning_poker (issue_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE planning_poker DROP FOREIGN KEY FK_2F1AE12B5E7AA58C');
        $this->addSql('DROP INDEX IDX_2F1AE12B5E7AA58C ON planning_poker');
        $this->addSql('ALTER TABLE planning_poker CHANGE issue_id task_id INT NOT NULL');
        $this->addSql('ALTER TABLE planning_poker ADD CONSTRAINT FK_2F1AE12B8DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
        $this->addSql('CREATE INDEX IDX_2F1AE12B8DB60186 ON planning_poker (task_id)');
    }
}
