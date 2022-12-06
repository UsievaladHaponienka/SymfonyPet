<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221206142618 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE invite (id INT AUTO_INCREMENT NOT NULL, profile_id INT NOT NULL, related_group_id INT NOT NULL, INDEX IDX_C7E210D7CCFA12B8 (profile_id), INDEX IDX_C7E210D758D797EA (related_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE invite ADD CONSTRAINT FK_C7E210D7CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE invite ADD CONSTRAINT FK_C7E210D758D797EA FOREIGN KEY (related_group_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE group_request DROP FOREIGN KEY FK_BD97DB938F2790D7');
        $this->addSql('DROP INDEX IDX_BD97DB938F2790D7 ON group_request');
        $this->addSql('ALTER TABLE group_request CHANGE requested_group_id related_group_id INT NOT NULL');
        $this->addSql('ALTER TABLE group_request ADD CONSTRAINT FK_BD97DB9358D797EA FOREIGN KEY (related_group_id) REFERENCES `group` (id)');
        $this->addSql('CREATE INDEX IDX_BD97DB9358D797EA ON group_request (related_group_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invite DROP FOREIGN KEY FK_C7E210D7CCFA12B8');
        $this->addSql('ALTER TABLE invite DROP FOREIGN KEY FK_C7E210D758D797EA');
        $this->addSql('DROP TABLE invite');
        $this->addSql('ALTER TABLE group_request DROP FOREIGN KEY FK_BD97DB9358D797EA');
        $this->addSql('DROP INDEX IDX_BD97DB9358D797EA ON group_request');
        $this->addSql('ALTER TABLE group_request CHANGE related_group_id requested_group_id INT NOT NULL');
        $this->addSql('ALTER TABLE group_request ADD CONSTRAINT FK_BD97DB938F2790D7 FOREIGN KEY (requested_group_id) REFERENCES `group` (id)');
        $this->addSql('CREATE INDEX IDX_BD97DB938F2790D7 ON group_request (requested_group_id)');
    }
}
