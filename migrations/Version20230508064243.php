<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230508064243 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D16C755722');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D18DE820D9');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D199E6F5DF');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D16C755722 FOREIGN KEY (buyer_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D18DE820D9 FOREIGN KEY (seller_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D199E6F5DF FOREIGN KEY (player_id) REFERENCES player (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D16C755722');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D18DE820D9');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D199E6F5DF');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D16C755722 FOREIGN KEY (buyer_id) REFERENCES team (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D18DE820D9 FOREIGN KEY (seller_id) REFERENCES team (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D199E6F5DF FOREIGN KEY (player_id) REFERENCES player (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
