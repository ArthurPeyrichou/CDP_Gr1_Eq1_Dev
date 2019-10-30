<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191030154830 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE member');
        $this->addSql('ALTER TABLE invitation MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE invitation DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE invitation DROP id, CHANGE date date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE invitation ADD PRIMARY KEY (member_id, project_id)');
        $this->addSql('ALTER TABLE project DROP project_id, CHANGE name name INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE member (id INT AUTO_INCREMENT NOT NULL, id_member INT NOT NULL, pseudo VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci, mail VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci, password VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE invitation DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE invitation ADD id INT AUTO_INCREMENT NOT NULL, CHANGE date date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE invitation ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE project ADD project_id INT NOT NULL, CHANGE name name VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
