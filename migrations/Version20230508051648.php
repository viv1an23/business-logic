<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230508051648 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, buyer_id INT NOT NULL, seller_id INT DEFAULT NULL, player_id INT DEFAULT NULL, price INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_723705D16C755722 (buyer_id), INDEX IDX_723705D18DE820D9 (seller_id), INDEX IDX_723705D199E6F5DF (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D16C755722 FOREIGN KEY (buyer_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D18DE820D9 FOREIGN KEY (seller_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D199E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D16C755722');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D18DE820D9');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D199E6F5DF');
        $this->addSql('DROP TABLE transaction');
    }
}
