<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191203213203 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE documentation (id INT AUTO_INCREMENT NOT NULL, project_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, INDEX IDX_73D5A93B166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invitation (id INT AUTO_INCREMENT NOT NULL, member_id INT NOT NULL, project_id INT NOT NULL, date DATETIME NOT NULL, invitation_key VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_F11D61A25C5A6C5C (invitation_key), INDEX IDX_F11D61A27597D3FE (member_id), INDEX IDX_F11D61A2166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE issue (id INT AUTO_INCREMENT NOT NULL, sprint_id INT DEFAULT NULL, project_id INT NOT NULL, number INT NOT NULL, description VARCHAR(200) NOT NULL, difficulty INT NOT NULL, priority VARCHAR(255) NOT NULL, INDEX IDX_12AD233E8C24077B (sprint_id), INDEX IDX_12AD233E166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE member (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, email_address VARCHAR(128) NOT NULL, password VARCHAR(255) NOT NULL, reset_token VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_70E4FA785E237E06 (name), UNIQUE INDEX UNIQ_70E4FA78B08E074E (email_address), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE member_project (member_id INT NOT NULL, project_id INT NOT NULL, INDEX IDX_9047677A7597D3FE (member_id), INDEX IDX_9047677A166D1F9C (project_id), PRIMARY KEY(member_id, project_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, member_id INT NOT NULL, description VARCHAR(255) NOT NULL, date DATETIME NOT NULL, INDEX IDX_BF5476CA7597D3FE (member_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planning_poker (id INT AUTO_INCREMENT NOT NULL, member_id INT NOT NULL, issue_id INT NOT NULL, creation_date DATE NOT NULL, value DOUBLE PRECISION NOT NULL, INDEX IDX_2F1AE12B7597D3FE (member_id), INDEX IDX_2F1AE12B5E7AA58C (issue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(200) NOT NULL, creation_date DATE NOT NULL, INDEX IDX_2FB3D0EE7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `release` (id INT AUTO_INCREMENT NOT NULL, project_id INT NOT NULL, number INT NOT NULL, description VARCHAR(255) NOT NULL, date DATE NOT NULL, link VARCHAR(255) NOT NULL, INDEX IDX_9E47031D166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sprint (id INT AUTO_INCREMENT NOT NULL, project_id INT NOT NULL, release_id INT DEFAULT NULL, number INT NOT NULL, description VARCHAR(256) NOT NULL, start_date DATE NOT NULL, duration_in_days INT NOT NULL, INDEX IDX_EF8055B7166D1F9C (project_id), UNIQUE INDEX UNIQ_EF8055B7B12A727D (release_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, developper_id INT DEFAULT NULL, sprint_id INT NOT NULL, number INT NOT NULL, description VARCHAR(255) NOT NULL, required_man_days DOUBLE PRECISION NOT NULL, status VARCHAR(8) NOT NULL, INDEX IDX_527EDB25DA42B93 (developper_id), INDEX IDX_527EDB258C24077B (sprint_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task_issue (task_id INT NOT NULL, issue_id INT NOT NULL, INDEX IDX_D509381E8DB60186 (task_id), INDEX IDX_D509381E5E7AA58C (issue_id), PRIMARY KEY(task_id, issue_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE test (id INT AUTO_INCREMENT NOT NULL, project_id INT NOT NULL, issue_id INT DEFAULT NULL, name VARCHAR(64) NOT NULL, description VARCHAR(255) NOT NULL, state VARCHAR(64) NOT NULL, INDEX IDX_D87F7E0C166D1F9C (project_id), INDEX IDX_D87F7E0C5E7AA58C (issue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE documentation ADD CONSTRAINT FK_73D5A93B166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT FK_F11D61A27597D3FE FOREIGN KEY (member_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT FK_F11D61A2166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233E8C24077B FOREIGN KEY (sprint_id) REFERENCES sprint (id)');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233E166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE member_project ADD CONSTRAINT FK_9047677A7597D3FE FOREIGN KEY (member_id) REFERENCES member (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE member_project ADD CONSTRAINT FK_9047677A166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA7597D3FE FOREIGN KEY (member_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE planning_poker ADD CONSTRAINT FK_2F1AE12B7597D3FE FOREIGN KEY (member_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE planning_poker ADD CONSTRAINT FK_2F1AE12B5E7AA58C FOREIGN KEY (issue_id) REFERENCES issue (id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE7E3C61F9 FOREIGN KEY (owner_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE `release` ADD CONSTRAINT FK_9E47031D166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE sprint ADD CONSTRAINT FK_EF8055B7166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE sprint ADD CONSTRAINT FK_EF8055B7B12A727D FOREIGN KEY (release_id) REFERENCES `release` (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25DA42B93 FOREIGN KEY (developper_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB258C24077B FOREIGN KEY (sprint_id) REFERENCES sprint (id)');
        $this->addSql('ALTER TABLE task_issue ADD CONSTRAINT FK_D509381E8DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task_issue ADD CONSTRAINT FK_D509381E5E7AA58C FOREIGN KEY (issue_id) REFERENCES issue (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE test ADD CONSTRAINT FK_D87F7E0C166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE test ADD CONSTRAINT FK_D87F7E0C5E7AA58C FOREIGN KEY (issue_id) REFERENCES issue (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE planning_poker DROP FOREIGN KEY FK_2F1AE12B5E7AA58C');
        $this->addSql('ALTER TABLE task_issue DROP FOREIGN KEY FK_D509381E5E7AA58C');
        $this->addSql('ALTER TABLE test DROP FOREIGN KEY FK_D87F7E0C5E7AA58C');
        $this->addSql('ALTER TABLE invitation DROP FOREIGN KEY FK_F11D61A27597D3FE');
        $this->addSql('ALTER TABLE member_project DROP FOREIGN KEY FK_9047677A7597D3FE');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA7597D3FE');
        $this->addSql('ALTER TABLE planning_poker DROP FOREIGN KEY FK_2F1AE12B7597D3FE');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE7E3C61F9');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25DA42B93');
        $this->addSql('ALTER TABLE documentation DROP FOREIGN KEY FK_73D5A93B166D1F9C');
        $this->addSql('ALTER TABLE invitation DROP FOREIGN KEY FK_F11D61A2166D1F9C');
        $this->addSql('ALTER TABLE issue DROP FOREIGN KEY FK_12AD233E166D1F9C');
        $this->addSql('ALTER TABLE member_project DROP FOREIGN KEY FK_9047677A166D1F9C');
        $this->addSql('ALTER TABLE `release` DROP FOREIGN KEY FK_9E47031D166D1F9C');
        $this->addSql('ALTER TABLE sprint DROP FOREIGN KEY FK_EF8055B7166D1F9C');
        $this->addSql('ALTER TABLE test DROP FOREIGN KEY FK_D87F7E0C166D1F9C');
        $this->addSql('ALTER TABLE sprint DROP FOREIGN KEY FK_EF8055B7B12A727D');
        $this->addSql('ALTER TABLE issue DROP FOREIGN KEY FK_12AD233E8C24077B');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB258C24077B');
        $this->addSql('ALTER TABLE task_issue DROP FOREIGN KEY FK_D509381E8DB60186');
        $this->addSql('DROP TABLE documentation');
        $this->addSql('DROP TABLE invitation');
        $this->addSql('DROP TABLE issue');
        $this->addSql('DROP TABLE member');
        $this->addSql('DROP TABLE member_project');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE planning_poker');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE `release`');
        $this->addSql('DROP TABLE sprint');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE task_issue');
        $this->addSql('DROP TABLE test');
    }
}
