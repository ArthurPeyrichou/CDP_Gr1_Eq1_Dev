<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191206032335 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE issue_sprint (issue_id INT NOT NULL, sprint_id INT NOT NULL, INDEX IDX_1D70DF905E7AA58C (issue_id), INDEX IDX_1D70DF908C24077B (sprint_id), PRIMARY KEY(issue_id, sprint_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE issue_sprint ADD CONSTRAINT FK_1D70DF905E7AA58C FOREIGN KEY (issue_id) REFERENCES issue (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE issue_sprint ADD CONSTRAINT FK_1D70DF908C24077B FOREIGN KEY (sprint_id) REFERENCES sprint (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE issue DROP FOREIGN KEY FK_12AD233E8C24077B');
        $this->addSql('DROP INDEX IDX_12AD233E8C24077B ON issue');
        $this->addSql('ALTER TABLE issue DROP sprint_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE issue_sprint');
        $this->addSql('ALTER TABLE issue ADD sprint_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233E8C24077B FOREIGN KEY (sprint_id) REFERENCES sprint (id)');
        $this->addSql('CREATE INDEX IDX_12AD233E8C24077B ON issue (sprint_id)');
    }
}
