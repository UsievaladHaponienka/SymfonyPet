<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221206144443 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE group_invites DROP FOREIGN KEY FK_857E3E60449B18CE');
        $this->addSql('ALTER TABLE group_invites DROP FOREIGN KEY FK_857E3E60CCFA12B8');
        $this->addSql('DROP TABLE group_invites');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE group_invites (id INT AUTO_INCREMENT NOT NULL, invite_group_id INT NOT NULL, profile_id INT NOT NULL, INDEX IDX_857E3E60449B18CE (invite_group_id), INDEX IDX_857E3E60CCFA12B8 (profile_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE group_invites ADD CONSTRAINT FK_857E3E60449B18CE FOREIGN KEY (invite_group_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE group_invites ADD CONSTRAINT FK_857E3E60CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
    }
}
