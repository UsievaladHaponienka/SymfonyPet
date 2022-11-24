<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221124084906 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE group_profile (group_id INT NOT NULL, profile_id INT NOT NULL, INDEX IDX_757FE03FE54D947 (group_id), INDEX IDX_757FE03CCFA12B8 (profile_id), PRIMARY KEY(group_id, profile_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE group_profile ADD CONSTRAINT FK_757FE03FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_profile ADD CONSTRAINT FK_757FE03CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE group_profile DROP FOREIGN KEY FK_757FE03FE54D947');
        $this->addSql('ALTER TABLE group_profile DROP FOREIGN KEY FK_757FE03CCFA12B8');
        $this->addSql('DROP TABLE group_profile');
    }
}
