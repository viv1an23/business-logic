<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230509061522 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE player (id INT AUTO_INCREMENT NOT NULL, team_id INT DEFAULT NULL, name VARCHAR(30) NOT NULL, surname VARCHAR(40) NOT NULL, price INT NOT NULL, is_available_for_sell TINYINT(1) DEFAULT 0, selling_price INT DEFAULT NULL, INDEX IDX_98197A65296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, country VARCHAR(70) NOT NULL, money_balance INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, buyer_id INT NOT NULL, seller_id INT DEFAULT NULL, player_id INT DEFAULT NULL, price INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_723705D16C755722 (buyer_id), INDEX IDX_723705D18DE820D9 (seller_id), INDEX IDX_723705D199E6F5DF (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A65296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D16C755722 FOREIGN KEY (buyer_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D18DE820D9 FOREIGN KEY (seller_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D199E6F5DF FOREIGN KEY (player_id) REFERENCES player (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE player DROP FOREIGN KEY FK_98197A65296CD8AE');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D16C755722');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D18DE820D9');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D199E6F5DF');
        $this->addSql('DROP TABLE player');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE user');
    }
}
