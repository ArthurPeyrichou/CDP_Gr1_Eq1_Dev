<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191114210509 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE invitation DROP INDEX UNIQ_F11D61A27597D3FE, ADD INDEX IDX_F11D61A27597D3FE (member_id)');
        $this->addSql('ALTER TABLE invitation DROP INDEX UNIQ_F11D61A2166D1F9C, ADD INDEX IDX_F11D61A2166D1F9C (project_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE invitation DROP INDEX IDX_F11D61A27597D3FE, ADD UNIQUE INDEX UNIQ_F11D61A27597D3FE (member_id)');
        $this->addSql('ALTER TABLE invitation DROP INDEX IDX_F11D61A2166D1F9C, ADD UNIQUE INDEX UNIQ_F11D61A2166D1F9C (project_id)');
    }
}
