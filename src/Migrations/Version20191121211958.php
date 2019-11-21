<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191121211958 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE issue DROP FOREIGN KEY FK_12AD233E1D0911AF');
        $this->addSql('DROP INDEX IDX_12AD233E1D0911AF ON issue');
        $this->addSql('ALTER TABLE issue ADD linked_release_id INT DEFAULT NULL, ADD number INT NOT NULL, DROP linked_to_release_id, DROP name');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233E26EEA93A FOREIGN KEY (linked_release_id) REFERENCES `release` (id)');
        $this->addSql('CREATE INDEX IDX_12AD233E26EEA93A ON issue (linked_release_id)');
        $this->addSql('ALTER TABLE sprint ADD number INT NOT NULL, DROP name');
        $this->addSql('ALTER TABLE task CHANGE developper_id developper_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE issue DROP FOREIGN KEY FK_12AD233E26EEA93A');
        $this->addSql('DROP INDEX IDX_12AD233E26EEA93A ON issue');
        $this->addSql('ALTER TABLE issue ADD linked_to_release_id INT DEFAULT NULL, ADD name VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP linked_release_id, DROP number');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233E1D0911AF FOREIGN KEY (linked_to_release_id) REFERENCES `release` (id)');
        $this->addSql('CREATE INDEX IDX_12AD233E1D0911AF ON issue (linked_to_release_id)');
        $this->addSql('ALTER TABLE sprint ADD name VARCHAR(64) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP number');
        $this->addSql('ALTER TABLE task CHANGE developper_id developper_id INT DEFAULT NULL');
    }
}
