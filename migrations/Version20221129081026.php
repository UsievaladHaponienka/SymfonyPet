<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221129081026 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE photo ADD post_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B784184B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_14B784184B89032C ON photo (post_id)');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D7E9E4C8C');
        $this->addSql('DROP INDEX UNIQ_5A8A6C8D7E9E4C8C ON post');
        $this->addSql('ALTER TABLE post DROP photo_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B784184B89032C');
        $this->addSql('DROP INDEX UNIQ_14B784184B89032C ON photo');
        $this->addSql('ALTER TABLE photo DROP post_id');
        $this->addSql('ALTER TABLE post ADD photo_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D7E9E4C8C FOREIGN KEY (photo_id) REFERENCES photo (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5A8A6C8D7E9E4C8C ON post (photo_id)');
    }
}
