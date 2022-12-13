<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221213122754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE album DROP FOREIGN KEY FK_39986E43FE54D947');
        $this->addSql('DROP INDEX IDX_39986E43FE54D947 ON album');
        $this->addSql('ALTER TABLE album CHANGE group_id related_group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_39986E4358D797EA FOREIGN KEY (related_group_id) REFERENCES `group` (id)');
        $this->addSql('CREATE INDEX IDX_39986E4358D797EA ON album (related_group_id)');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DFE54D947');
        $this->addSql('DROP INDEX IDX_5A8A6C8DFE54D947 ON post');
        $this->addSql('ALTER TABLE post CHANGE group_id related_group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D58D797EA FOREIGN KEY (related_group_id) REFERENCES `group` (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D58D797EA ON post (related_group_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D58D797EA');
        $this->addSql('DROP INDEX IDX_5A8A6C8D58D797EA ON post');
        $this->addSql('ALTER TABLE post CHANGE related_group_id group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DFE54D947 ON post (group_id)');
        $this->addSql('ALTER TABLE album DROP FOREIGN KEY FK_39986E4358D797EA');
        $this->addSql('DROP INDEX IDX_39986E4358D797EA ON album');
        $this->addSql('ALTER TABLE album CHANGE related_group_id group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_39986E43FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id)');
        $this->addSql('CREATE INDEX IDX_39986E43FE54D947 ON album (group_id)');
    }
}
