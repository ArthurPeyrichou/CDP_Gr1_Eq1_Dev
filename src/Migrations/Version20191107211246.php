<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191107211246 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE invitation DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE invitation ADD id INT AUTO_INCREMENT NOT NULL, CHANGE date date DATETIME NOT NULL, CHANGE inv_key invitation_key VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT FK_F11D61A27597D3FE FOREIGN KEY (member_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT FK_F11D61A2166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F11D61A25C5A6C5C ON invitation (invitation_key)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F11D61A27597D3FE ON invitation (member_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F11D61A2166D1F9C ON invitation (project_id)');
        $this->addSql('ALTER TABLE invitation ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE invitation MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE invitation DROP FOREIGN KEY FK_F11D61A27597D3FE');
        $this->addSql('ALTER TABLE invitation DROP FOREIGN KEY FK_F11D61A2166D1F9C');
        $this->addSql('DROP INDEX UNIQ_F11D61A25C5A6C5C ON invitation');
        $this->addSql('DROP INDEX UNIQ_F11D61A27597D3FE ON invitation');
        $this->addSql('DROP INDEX UNIQ_F11D61A2166D1F9C ON invitation');
        $this->addSql('ALTER TABLE invitation DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE invitation DROP id, CHANGE date date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE invitation_key inv_key VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE invitation ADD PRIMARY KEY (member_id, project_id)');
    }
}
