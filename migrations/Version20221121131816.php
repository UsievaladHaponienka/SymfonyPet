<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221121131816 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE album DROP FOREIGN KEY FK_39986E4388900185');
        $this->addSql('ALTER TABLE album DROP FOREIGN KEY FK_39986E432F68B530');
        $this->addSql('DROP INDEX IDX_39986E4388900185 ON album');
        $this->addSql('DROP INDEX IDX_39986E432F68B530 ON album');
        $this->addSql('ALTER TABLE album ADD profile_id INT DEFAULT NULL, ADD group_id INT DEFAULT NULL, DROP profile_id_id, DROP group_id_id');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_39986E43CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_39986E43FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id)');
        $this->addSql('CREATE INDEX IDX_39986E43CCFA12B8 ON album (profile_id)');
        $this->addSql('CREATE INDEX IDX_39986E43FE54D947 ON album (group_id)');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C88900185');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CE85F12B8');
        $this->addSql('DROP INDEX IDX_9474526C88900185 ON comment');
        $this->addSql('DROP INDEX IDX_9474526CE85F12B8 ON comment');
        $this->addSql('ALTER TABLE comment ADD post_id INT NOT NULL, ADD profile_id INT NOT NULL, DROP post_id_id, DROP profile_id_id');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C4B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CCCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('CREATE INDEX IDX_9474526C4B89032C ON comment (post_id)');
        $this->addSql('CREATE INDEX IDX_9474526CCCFA12B8 ON comment (profile_id)');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B388900185');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B3E85F12B8');
        $this->addSql('DROP INDEX IDX_AC6340B388900185 ON `like`');
        $this->addSql('DROP INDEX IDX_AC6340B3E85F12B8 ON `like`');
        $this->addSql('ALTER TABLE `like` ADD post_id INT NOT NULL, ADD profile_id INT NOT NULL, DROP post_id_id, DROP profile_id_id');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B34B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('CREATE INDEX IDX_AC6340B34B89032C ON `like` (post_id)');
        $this->addSql('CREATE INDEX IDX_AC6340B3CCFA12B8 ON `like` (profile_id)');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B784189FCD471');
        $this->addSql('DROP INDEX IDX_14B784189FCD471 ON photo');
        $this->addSql('ALTER TABLE photo CHANGE album_id_id album_id INT NOT NULL');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B784181137ABCF FOREIGN KEY (album_id) REFERENCES album (id)');
        $this->addSql('CREATE INDEX IDX_14B784181137ABCF ON photo (album_id)');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D2F68B530');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D88900185');
        $this->addSql('DROP INDEX IDX_5A8A6C8D88900185 ON post');
        $this->addSql('DROP INDEX IDX_5A8A6C8D2F68B530 ON post');
        $this->addSql('ALTER TABLE post ADD profile_id INT DEFAULT NULL, ADD group_id INT DEFAULT NULL, DROP profile_id_id, DROP group_id_id');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DCCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DCCFA12B8 ON post (profile_id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DFE54D947 ON post (group_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B34B89032C');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B3CCFA12B8');
        $this->addSql('DROP INDEX IDX_AC6340B34B89032C ON `like`');
        $this->addSql('DROP INDEX IDX_AC6340B3CCFA12B8 ON `like`');
        $this->addSql('ALTER TABLE `like` ADD post_id_id INT NOT NULL, ADD profile_id_id INT NOT NULL, DROP post_id, DROP profile_id');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B388900185 FOREIGN KEY (profile_id_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3E85F12B8 FOREIGN KEY (post_id_id) REFERENCES post (id)');
        $this->addSql('CREATE INDEX IDX_AC6340B388900185 ON `like` (profile_id_id)');
        $this->addSql('CREATE INDEX IDX_AC6340B3E85F12B8 ON `like` (post_id_id)');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C4B89032C');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CCCFA12B8');
        $this->addSql('DROP INDEX IDX_9474526C4B89032C ON comment');
        $this->addSql('DROP INDEX IDX_9474526CCCFA12B8 ON comment');
        $this->addSql('ALTER TABLE comment ADD post_id_id INT NOT NULL, ADD profile_id_id INT NOT NULL, DROP post_id, DROP profile_id');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C88900185 FOREIGN KEY (profile_id_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CE85F12B8 FOREIGN KEY (post_id_id) REFERENCES post (id)');
        $this->addSql('CREATE INDEX IDX_9474526C88900185 ON comment (profile_id_id)');
        $this->addSql('CREATE INDEX IDX_9474526CE85F12B8 ON comment (post_id_id)');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B784181137ABCF');
        $this->addSql('DROP INDEX IDX_14B784181137ABCF ON photo');
        $this->addSql('ALTER TABLE photo CHANGE album_id album_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B784189FCD471 FOREIGN KEY (album_id_id) REFERENCES album (id)');
        $this->addSql('CREATE INDEX IDX_14B784189FCD471 ON photo (album_id_id)');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DCCFA12B8');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DFE54D947');
        $this->addSql('DROP INDEX IDX_5A8A6C8DCCFA12B8 ON post');
        $this->addSql('DROP INDEX IDX_5A8A6C8DFE54D947 ON post');
        $this->addSql('ALTER TABLE post ADD profile_id_id INT DEFAULT NULL, ADD group_id_id INT DEFAULT NULL, DROP profile_id, DROP group_id');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D2F68B530 FOREIGN KEY (group_id_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D88900185 FOREIGN KEY (profile_id_id) REFERENCES profile (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D88900185 ON post (profile_id_id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D2F68B530 ON post (group_id_id)');
        $this->addSql('ALTER TABLE album DROP FOREIGN KEY FK_39986E43CCFA12B8');
        $this->addSql('ALTER TABLE album DROP FOREIGN KEY FK_39986E43FE54D947');
        $this->addSql('DROP INDEX IDX_39986E43CCFA12B8 ON album');
        $this->addSql('DROP INDEX IDX_39986E43FE54D947 ON album');
        $this->addSql('ALTER TABLE album ADD profile_id_id INT DEFAULT NULL, ADD group_id_id INT DEFAULT NULL, DROP profile_id, DROP group_id');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_39986E4388900185 FOREIGN KEY (profile_id_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_39986E432F68B530 FOREIGN KEY (group_id_id) REFERENCES `group` (id)');
        $this->addSql('CREATE INDEX IDX_39986E4388900185 ON album (profile_id_id)');
        $this->addSql('CREATE INDEX IDX_39986E432F68B530 ON album (group_id_id)');
    }
}
