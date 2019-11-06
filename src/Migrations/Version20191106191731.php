<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191106191731 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE invitation (member_id INT NOT NULL, project_id INT NOT NULL, date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, inv_key VARCHAR(50) NOT NULL, PRIMARY KEY(member_id, project_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE member (id INT AUTO_INCREMENT NOT NULL, pseudo VARCHAR(50) NOT NULL, mail VARCHAR(50) NOT NULL, password VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_70E4FA7886CC499D (pseudo), UNIQUE INDEX UNIQ_70E4FA785126AC48 (mail), UNIQUE INDEX UNIQ_70E4FA7876348E1B67B341FC (PSEUDO, MAIL), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE member_project (member_id INT NOT NULL, project_id INT NOT NULL, INDEX IDX_9047677A7597D3FE (member_id), INDEX IDX_9047677A166D1F9C (project_id), PRIMARY KEY(member_id, project_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(200) NOT NULL, creation_date DATE NOT NULL, owner INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE member_project ADD CONSTRAINT FK_9047677A7597D3FE FOREIGN KEY (member_id) REFERENCES member (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE member_project ADD CONSTRAINT FK_9047677A166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE member_project DROP FOREIGN KEY FK_9047677A7597D3FE');
        $this->addSql('ALTER TABLE member_project DROP FOREIGN KEY FK_9047677A166D1F9C');
        $this->addSql('DROP TABLE invitation');
        $this->addSql('DROP TABLE member');
        $this->addSql('DROP TABLE member_project');
        $this->addSql('DROP TABLE project');
    }
}
