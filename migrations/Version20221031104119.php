<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221031104119 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE album (id INT AUTO_INCREMENT NOT NULL, profile_id_id INT DEFAULT NULL, group_id_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_39986E4388900185 (profile_id_id), INDEX IDX_39986E432F68B530 (group_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, post_id_id INT NOT NULL, profile_id_id INT NOT NULL, content LONGTEXT DEFAULT NULL, image_url VARCHAR(255) DEFAULT NULL, INDEX IDX_9474526CE85F12B8 (post_id_id), INDEX IDX_9474526C88900185 (profile_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `group` (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, url VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, group_image_url VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `like` (id INT AUTO_INCREMENT NOT NULL, post_id_id INT NOT NULL, profile_id_id INT NOT NULL, INDEX IDX_AC6340B3E85F12B8 (post_id_id), INDEX IDX_AC6340B388900185 (profile_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE photo (id INT AUTO_INCREMENT NOT NULL, album_id_id INT NOT NULL, image_url VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_14B784189FCD471 (album_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, profile_id_id INT DEFAULT NULL, group_id_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, content LONGTEXT DEFAULT NULL, is_edited TINYINT(1) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_5A8A6C8D88900185 (profile_id_id), INDEX IDX_5A8A6C8D2F68B530 (group_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profile (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, username VARCHAR(255) NOT NULL, profile_image_url VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_8157AA0F9D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_39986E4388900185 FOREIGN KEY (profile_id_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_39986E432F68B530 FOREIGN KEY (group_id_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CE85F12B8 FOREIGN KEY (post_id_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C88900185 FOREIGN KEY (profile_id_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3E85F12B8 FOREIGN KEY (post_id_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B388900185 FOREIGN KEY (profile_id_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B784189FCD471 FOREIGN KEY (album_id_id) REFERENCES album (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D88900185 FOREIGN KEY (profile_id_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D2F68B530 FOREIGN KEY (group_id_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE profile ADD CONSTRAINT FK_8157AA0F9D86650F FOREIGN KEY (user_id_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE album DROP FOREIGN KEY FK_39986E4388900185');
        $this->addSql('ALTER TABLE album DROP FOREIGN KEY FK_39986E432F68B530');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CE85F12B8');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C88900185');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B3E85F12B8');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B388900185');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B784189FCD471');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D88900185');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D2F68B530');
        $this->addSql('ALTER TABLE profile DROP FOREIGN KEY FK_8157AA0F9D86650F');
        $this->addSql('DROP TABLE album');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE `group`');
        $this->addSql('DROP TABLE `like`');
        $this->addSql('DROP TABLE photo');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE profile');
    }
}
