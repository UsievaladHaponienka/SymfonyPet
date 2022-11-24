<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221124094135 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE friendship_request (id INT AUTO_INCREMENT NOT NULL, requester_id INT NOT NULL, requestee_id INT NOT NULL, INDEX IDX_6CC48EE1ED442CF4 (requester_id), INDEX IDX_6CC48EE1208A43D2 (requestee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE friendship_request ADD CONSTRAINT FK_6CC48EE1ED442CF4 FOREIGN KEY (requester_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE friendship_request ADD CONSTRAINT FK_6CC48EE1208A43D2 FOREIGN KEY (requestee_id) REFERENCES profile (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE friendship_request DROP FOREIGN KEY FK_6CC48EE1ED442CF4');
        $this->addSql('ALTER TABLE friendship_request DROP FOREIGN KEY FK_6CC48EE1208A43D2');
        $this->addSql('DROP TABLE friendship_request');
    }
}
