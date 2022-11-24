<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221124112142 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE friendship (id INT AUTO_INCREMENT NOT NULL, profile_id INT NOT NULL, friend_id INT NOT NULL, INDEX IDX_7234A45FCCFA12B8 (profile_id), INDEX IDX_7234A45F6A5458E8 (friend_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE friendship ADD CONSTRAINT FK_7234A45FCCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE friendship ADD CONSTRAINT FK_7234A45F6A5458E8 FOREIGN KEY (friend_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE profile DROP friends_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE friendship DROP FOREIGN KEY FK_7234A45FCCFA12B8');
        $this->addSql('ALTER TABLE friendship DROP FOREIGN KEY FK_7234A45F6A5458E8');
        $this->addSql('DROP TABLE friendship');
        $this->addSql('ALTER TABLE profile ADD friends_id INT NOT NULL');
    }
}
