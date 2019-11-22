<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191122142907 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sprint CHANGE release_id release_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE task CHANGE developper_id developper_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE test ADD issue_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE test ADD CONSTRAINT FK_D87F7E0C5E7AA58C FOREIGN KEY (issue_id) REFERENCES issue (id)');
        $this->addSql('CREATE INDEX IDX_D87F7E0C5E7AA58C ON test (issue_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sprint CHANGE release_id release_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE task CHANGE developper_id developper_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE test DROP FOREIGN KEY FK_D87F7E0C5E7AA58C');
        $this->addSql('DROP INDEX IDX_D87F7E0C5E7AA58C ON test');
        $this->addSql('ALTER TABLE test DROP issue_id');
    }
}
