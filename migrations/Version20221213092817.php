<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221213092817 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE album (id INT AUTO_INCREMENT NOT NULL, profile_id INT DEFAULT NULL, group_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_39986E43CCFA12B8 (profile_id), INDEX IDX_39986E43FE54D947 (group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, post_id INT DEFAULT NULL, profile_id INT NOT NULL, discussion_id INT DEFAULT NULL, content LONGTEXT NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_9474526C4B89032C (post_id), INDEX IDX_9474526CCCFA12B8 (profile_id), INDEX IDX_9474526C1ADED311 (discussion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discussion (id INT AUTO_INCREMENT NOT NULL, related_group_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_C0B9F90F58D797EA (related_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE friendship (id INT AUTO_INCREMENT NOT NULL, profile_id INT NOT NULL, friend_id INT NOT NULL, INDEX IDX_7234A45FCCFA12B8 (profile_id), INDEX IDX_7234A45F6A5458E8 (friend_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE friendship_request (id INT AUTO_INCREMENT NOT NULL, requester_id INT NOT NULL, requestee_id INT NOT NULL, INDEX IDX_6CC48EE1ED442CF4 (requester_id), INDEX IDX_6CC48EE1208A43D2 (requestee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `group` (id INT AUTO_INCREMENT NOT NULL, admin_id INT NOT NULL, title VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, group_image_url VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_6DC044C5642B8210 (admin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE group_profile (group_id INT NOT NULL, profile_id INT NOT NULL, INDEX IDX_757FE03FE54D947 (group_id), INDEX IDX_757FE03CCFA12B8 (profile_id), PRIMARY KEY(group_id, profile_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE group_request (id INT AUTO_INCREMENT NOT NULL, related_group_id INT NOT NULL, profile_id INT NOT NULL, INDEX IDX_BD97DB9358D797EA (related_group_id), INDEX IDX_BD97DB93CCFA12B8 (profile_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invite (id INT AUTO_INCREMENT NOT NULL, profile_id INT NOT NULL, related_group_id INT NOT NULL, INDEX IDX_C7E210D7CCFA12B8 (profile_id), INDEX IDX_C7E210D758D797EA (related_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `like` (id INT AUTO_INCREMENT NOT NULL, post_id INT DEFAULT NULL, profile_id INT NOT NULL, comment_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_AC6340B34B89032C (post_id), INDEX IDX_AC6340B3CCFA12B8 (profile_id), INDEX IDX_AC6340B3F8697D13 (comment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE photo (id INT AUTO_INCREMENT NOT NULL, album_id INT NOT NULL, post_id INT DEFAULT NULL, image_url VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', description LONGTEXT DEFAULT NULL, INDEX IDX_14B784181137ABCF (album_id), UNIQUE INDEX UNIQ_14B784184B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, profile_id INT DEFAULT NULL, group_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, content LONGTEXT DEFAULT NULL, is_edited TINYINT(1) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_5A8A6C8DCCFA12B8 (profile_id), INDEX IDX_5A8A6C8DFE54D947 (group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profile (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, username VARCHAR(255) DEFAULT NULL, profile_image_url VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_8157AA0FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_39986E43CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_39986E43FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C4B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CCCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C1ADED311 FOREIGN KEY (discussion_id) REFERENCES discussion (id)');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90F58D797EA FOREIGN KEY (related_group_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE friendship ADD CONSTRAINT FK_7234A45FCCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE friendship ADD CONSTRAINT FK_7234A45F6A5458E8 FOREIGN KEY (friend_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE friendship_request ADD CONSTRAINT FK_6CC48EE1ED442CF4 FOREIGN KEY (requester_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE friendship_request ADD CONSTRAINT FK_6CC48EE1208A43D2 FOREIGN KEY (requestee_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C5642B8210 FOREIGN KEY (admin_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE group_profile ADD CONSTRAINT FK_757FE03FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_profile ADD CONSTRAINT FK_757FE03CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_request ADD CONSTRAINT FK_BD97DB9358D797EA FOREIGN KEY (related_group_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE group_request ADD CONSTRAINT FK_BD97DB93CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE invite ADD CONSTRAINT FK_C7E210D7CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE invite ADD CONSTRAINT FK_C7E210D758D797EA FOREIGN KEY (related_group_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B34B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3F8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id)');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B784181137ABCF FOREIGN KEY (album_id) REFERENCES album (id)');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B784184B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DCCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE profile ADD CONSTRAINT FK_8157AA0FA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE album DROP FOREIGN KEY FK_39986E43CCFA12B8');
        $this->addSql('ALTER TABLE album DROP FOREIGN KEY FK_39986E43FE54D947');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C4B89032C');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CCCFA12B8');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C1ADED311');
        $this->addSql('ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90F58D797EA');
        $this->addSql('ALTER TABLE friendship DROP FOREIGN KEY FK_7234A45FCCFA12B8');
        $this->addSql('ALTER TABLE friendship DROP FOREIGN KEY FK_7234A45F6A5458E8');
        $this->addSql('ALTER TABLE friendship_request DROP FOREIGN KEY FK_6CC48EE1ED442CF4');
        $this->addSql('ALTER TABLE friendship_request DROP FOREIGN KEY FK_6CC48EE1208A43D2');
        $this->addSql('ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C5642B8210');
        $this->addSql('ALTER TABLE group_profile DROP FOREIGN KEY FK_757FE03FE54D947');
        $this->addSql('ALTER TABLE group_profile DROP FOREIGN KEY FK_757FE03CCFA12B8');
        $this->addSql('ALTER TABLE group_request DROP FOREIGN KEY FK_BD97DB9358D797EA');
        $this->addSql('ALTER TABLE group_request DROP FOREIGN KEY FK_BD97DB93CCFA12B8');
        $this->addSql('ALTER TABLE invite DROP FOREIGN KEY FK_C7E210D7CCFA12B8');
        $this->addSql('ALTER TABLE invite DROP FOREIGN KEY FK_C7E210D758D797EA');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B34B89032C');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B3CCFA12B8');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B3F8697D13');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B784181137ABCF');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B784184B89032C');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DCCFA12B8');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DFE54D947');
        $this->addSql('ALTER TABLE profile DROP FOREIGN KEY FK_8157AA0FA76ED395');
        $this->addSql('DROP TABLE album');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE discussion');
        $this->addSql('DROP TABLE friendship');
        $this->addSql('DROP TABLE friendship_request');
        $this->addSql('DROP TABLE `group`');
        $this->addSql('DROP TABLE group_profile');
        $this->addSql('DROP TABLE group_request');
        $this->addSql('DROP TABLE invite');
        $this->addSql('DROP TABLE `like`');
        $this->addSql('DROP TABLE photo');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE profile');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
